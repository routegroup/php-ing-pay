<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Response;

use Routegroup\IngPay\Http\Response as HttpResponse;
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Value\Transaction;

function transactionBody(bool $action): string
{
    $body = ['transaction' => Transaction::factory()->asApiPending()->make()->toArray()];

    if ($action) {
        $body['action'] = ['type' => 'redirect', 'url' => 'https://paywall.example.test/pay', 'method' => 'GET'];
    }

    return json_encode($body, JSON_THROW_ON_ERROR);
}

// A BLIK code is collected by the merchant, so the gateway has nowhere to send
// the payer and omits the key the redirect methods carry.
it('hydrates a transaction the gateway answered with no action', function (): void {
    $response = new TransactionResponse(new HttpResponse(200, transactionBody(action: false)));

    expect($response->action)->toBeNull()
        ->and($response->transaction->id)->not->toBeEmpty();
});

it('still hydrates the action when the gateway sends one', function (): void {
    $response = new TransactionResponse(new HttpResponse(200, transactionBody(action: true)));

    expect($response->action?->url)->toBe('https://paywall.example.test/pay');
});

it('builds both shapes from the factory', function (): void {
    expect(TransactionResponse::factory()->withoutAction()->make()->action)->toBeNull()
        ->and(TransactionResponse::factory()->make()->action)->not->toBeNull();
});
