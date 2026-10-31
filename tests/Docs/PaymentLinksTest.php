<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs\PaymentLinks;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\CancelPaymentResponse;
use Routegroup\IngPay\Response\GetPaymentResponse;
use Routegroup\IngPay\Response\PaymentResponse;
use Routegroup\IngPay\Types\Environment;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Payment;

/** The samples on the Payment Links page, run in the order the page shows them. */
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

it('creates, reads and cancels a payment link', function (): void {
    $client = new FakeClient()
        ->queue(PaymentResponse::factory()->make([
            'payment' => Payment::factory()->make(['id' => 'pay-1', 'url' => 'https://paywall.example/pay/pay-1']),
        ])->toArray())
        ->queue(GetPaymentResponse::factory()->make(['id' => 'pay-1', 'status' => TransactionStatus::SETTLED])->toArray())
        ->queue(CancelPaymentResponse::factory()->make(['id' => 'pay-1'])->toArray());
    $api = new Api($client, config(), new Url(config()));

    $response = require __DIR__.'/samples/payment-links/create.php';

    expect($client->lastRequest()?->body)
        ->toMatchArray(['amount' => 4999, 'orderId' => 'ORDER-1042', 'serviceId' => 'service-1'])
        ->and($response->payment->url)->toBe('https://paywall.example/pay/pay-1');

    $payment = require __DIR__.'/samples/payment-links/read.php';

    expect($client->lastRequest()?->url)->toEndWith('/payment/pay-1')
        ->and($payment->status)->toBe(TransactionStatus::SETTLED);

    $cancelled = require __DIR__.'/samples/payment-links/cancel.php';

    expect($client->lastRequest()?->body)->toMatchArray(['paymentId' => 'pay-1'])
        ->and($cancelled->status)->toBe(TransactionStatus::CANCELLED);
});

it('tests against a fake client', function (): void {
    $config = config();

    $client = require __DIR__.'/samples/payment-links/testing.php';

    expect($client->sentCount())->toBe(1);
});
