<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests;

use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;
use Routegroup\IngPay\Types\Environment;
use Routegroup\IngPay\Types\HashMethod;
use Routegroup\IngPay\Types\Lang;
use Routegroup\IngPay\Value\Cart;

function paywallConfig(): Config
{
    return new Config(
        merchantId: '$merchant_id$',
        serviceId: '$service_id$',
        serviceKey: '$service_key$',
        apiKey: '$api_key$',
        env: Environment::SANDBOX,
    );
}

function paywall(): Paywall
{
    return new Paywall(paywallConfig(), new Utils(paywallConfig()));
}

/** @return array<string, string> */
function queryOf(string $url): array
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $fields);

    /** @var array<string, string> $fields */
    return $fields;
}

it('builds a url on the configured paywall host', function (): void {
    $url = paywall()->url(CreateTransactionRequest::factory()->make());

    expect($url)->toStartWith('https://paywall.sandbox.pay.ing.pl/payment?');
});

it('puts the language in the path when one is asked for', function (): void {
    $url = paywall()->url(CreateTransactionRequest::factory()->make(), Lang::PL);

    expect($url)->toStartWith('https://paywall.sandbox.pay.ing.pl/pl/payment?');
});

it('carries the configured merchant and service', function (): void {
    $request = CreateTransactionRequest::factory()->make(['merchantId' => null, 'serviceId' => null]);

    $fields = queryOf(paywall()->url($request));

    expect($fields['merchantId'])->toBe('$merchant_id$')
        ->and($fields['serviceId'])->toBe('$service_id$');
});

it('signs the url with a signature that verifies back', function (): void {
    $fields = queryOf(paywall()->url(CreateTransactionRequest::factory()->make()));

    $signature = $fields['signature'];
    unset($fields['signature']);

    expect($signature)->toBe(new Utils(paywallConfig())->createSignature($fields, HashMethod::SHA256));
});

it('signs with the hash method asked for', function (): void {
    $fields = paywall()->fields(CreateTransactionRequest::factory()->make(), HashMethod::SHA512);

    expect($fields['signature'])->toEndWith(';sha512');
});

it('encodes a cart rather than expanding it into the query', function (): void {
    $cart = Cart::factory()->make();

    $fields = queryOf(paywall()->url(
        CreateTransactionRequest::factory()->make(['cart' => $cart])
    ));

    expect($fields['cart'])->toBe($cart->encoded())
        ->and($fields)->not->toHaveKey('cart[items][0][name]');
});

it('renders the url from the same fields it signs', function (): void {
    $request = CreateTransactionRequest::factory()->make();

    expect(queryOf(paywall()->url($request)))->toEqual(paywall()->fields($request));
});

it('encodes the query as RFC 3986', function (): void {
    // A space as %20, not +: the url Laravel's Arr::query() gave before the
    // builder moved here, so a redirect built either way is the same.
    $url = paywall()->url(CreateTransactionRequest::factory()->make(['customerFirstName' => 'Anna Maria']));

    expect($url)->toContain('customerFirstName=Anna%20Maria')
        ->and($url)->not->toContain('Anna+Maria');
});
