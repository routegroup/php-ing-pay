<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs\CartsAndInvoices;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\PaymentResponse;
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Types\Environment;

/** The samples on the Carts & Invoices page, run in the order the page shows them. */
function config(): Config
{
    return new Config(
        merchantId: 'merchant-1',
        serviceId: 'service-1',
        serviceKey: 'key',
        apiKey: 'token-abc',
        env: Environment::SANDBOX,
    );
}

it('sends a cart with a payment link and an invoice with a transaction, or both through the paywall', function (): void {
    $config = config();
    $client = new FakeClient()
        ->queue(PaymentResponse::factory()->make()->toArray())
        ->queue(TransactionResponse::factory()->make()->toArray());
    $api = new Api($client, $config, new Url($config));

    $cart = require __DIR__.'/samples/carts-and-invoices/cart.php';

    expect($client->lastRequest()?->body)
        ->toMatchArray(['preselectMethodCode' => 'lease_now'])
        ->and($client->lastRequest()?->body['cart']['items'][0])
        ->toMatchArray(['id' => 'SKU-1', 'amount' => 300000, 'tax' => 69000, 'taxStake' => 23]);

    $invoice = require __DIR__.'/samples/carts-and-invoices/invoice.php';

    expect($client->lastRequest()?->body['invoice'])
        ->toMatchArray(['buyer' => $invoice->buyer->toArray()])
        ->and($client->lastRequest()?->body['invoice']['positions'][0])
        ->toMatchArray(['grossAmount' => 369000, 'taxStake' => 'TAX_23']);

    $url = require __DIR__.'/samples/carts-and-invoices/paywall.php';

    parse_str((string) parse_url($url, PHP_URL_QUERY), $fields);

    expect($fields)->toMatchArray(['cart' => $cart->encoded(), 'invoice' => $invoice->encoded()])
        ->toHaveKey('signature');
});

it('names the field an incomplete cart is missing', function (): void {
    $field = require __DIR__.'/samples/carts-and-invoices/invalid.php';

    expect($field)->toBe('items.amount');
});

it('tests against a fake client', function (): void {
    $config = config();

    $client = require __DIR__.'/samples/carts-and-invoices/testing.php';

    expect($client->sentCount())->toBe(1);
});
