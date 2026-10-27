<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Response;

use Routegroup\IngPay\Http\Response as HttpResponse;
use Routegroup\IngPay\Response\PaymentMethodsResponse;
use Routegroup\IngPay\Types\PaymentMethod;

/**
 * The shape below is what the sandbox answers with, taken from a probe against
 * it rather than from the specification — which declares a bare list and so
 * describes something the gateway does not send.
 */
function envelope(): string
{
    return json_encode([
        'timestamp' => 1793088213695,
        'data' => [[
            'label' => 'Płatność BLIK',
            'isOnline' => true,
            'priority' => 1,
            'channels' => [[
                'paymentMethod' => 'blik',
                'paymentMethodCode' => 'blik',
                'label' => 'Płatność BLIK',
                'priority' => 1,
                'isOnline' => true,
            ]],
        ]],
    ], JSON_THROW_ON_ERROR);
}

it('unwraps the envelope the gateway actually sends', function (): void {
    $response = new PaymentMethodsResponse(new HttpResponse(200, envelope()));

    expect($response->methods)->toHaveCount(1)
        ->and($response->methods[0]->label)->toBe('Płatność BLIK')
        ->and($response->timestamp)->toBe(1793088213695);
});

it('hydrates the channels inside a method group', function (): void {
    $response = new PaymentMethodsResponse(new HttpResponse(200, envelope()));

    expect($response->methods[0]->channels[0]->paymentMethod)->toBe(PaymentMethod::BLIK);
});

it('still accepts the bare list the specification declares', function (): void {
    $bare = json_encode([['label' => 'Płatność BLIK', 'channels' => []]], JSON_THROW_ON_ERROR);

    $response = new PaymentMethodsResponse(new HttpResponse(200, $bare));

    expect($response->methods)->toHaveCount(1)
        ->and($response->timestamp)->toBeNull();
});

it('gives an empty list for a shape it has never seen', function (): void {
    $response = new PaymentMethodsResponse(new HttpResponse(200, '{"unexpected":true}'));

    expect($response->methods)->toBe([]);
});
