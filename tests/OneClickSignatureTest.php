<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests;

use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Paywall\OneClickRequest;
use Routegroup\IngPay\Types\HashMethod;
use Routegroup\IngPay\Types\WidgetType;

function widgetRequest(): OneClickRequest
{
    return OneClickRequest::factory()->make([
        'amount' => 1000,
        'orderId' => 'WIDGET-1',
        'locale' => 'pl',
        'inline' => true,
        'elementId' => 'card-widget',
        'elementEvent' => 'click',
    ]);
}

it('defaults the widget type to one-click', function (): void {
    expect((new OneClickRequest)->widgetType)->toBe(WidgetType::ONECLICK);
});

it('leaves the presentation fields out of the signature', function (): void {
    $fields = queryOf(paywall()->url(widgetRequest()));

    $signed = array_diff_key($fields, array_flip(OneClickRequest::UNSIGNED), ['signature' => '']);

    expect($fields['signature'])->toBe(new Utils(paywallConfig())->createSignature($signed, HashMethod::SHA256));
});

it('would produce a different signature if they were included', function (): void {
    // The point of the test above: without the exclusion the digest changes,
    // and the gateway cannot reproduce it.
    $fields = queryOf(paywall()->url(widgetRequest()));

    $all = $fields;
    unset($all['signature']);

    expect($fields['signature'])->not->toBe(new Utils(paywallConfig())->createSignature($all, HashMethod::SHA256));
});

it('still carries the presentation fields in the url', function (): void {
    $fields = queryOf(paywall()->url(widgetRequest()));

    expect($fields)->toHaveKeys(['locale', 'elementId', 'elementEvent']);
});
