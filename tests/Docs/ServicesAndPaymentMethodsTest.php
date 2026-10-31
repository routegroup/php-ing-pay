<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\PaymentMethodsResponse;
use Routegroup\IngPay\Response\ServiceResponse;
use Routegroup\IngPay\Response\ServicesResponse;
use Routegroup\IngPay\Types\Environment;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Value\ServiceData;
use Routegroup\IngPay\Value\ServicePaymentMethod;

/**
 * The samples on the Services & Payment Methods page. The list endpoints
 * answer with a bare list or an envelope, so the fake answers with the
 * factory's own HTTP response rather than with toArray().
 */
function servicesConfig(): Config
{
    return new Config(
        merchantId: 'merchant-1',
        serviceId: 'service-1',
        serviceKey: 'key',
        apiKey: 'token-abc',
        env: Environment::SANDBOX,
    );
}

it('lists the active services', function (): void {
    $client = new FakeClient(ServicesResponse::factory()->make()->http());
    $api = new Api($client, servicesConfig(), new Url(servicesConfig()));

    $active = require __DIR__.'/samples/services-and-payment-methods/services.php';

    expect($active)->toHaveCount(1)
        ->and($client->lastRequest()?->url)->toEndWith('/merchant/merchant-1/services');
});

it('keeps the payment methods a service has switched on', function (): void {
    $client = FakeClient::answering(ServiceResponse::factory()->make([
        'service' => ServiceData::factory()->make([
            'paymentMethods' => [
                ServicePaymentMethod::factory()->make(['isActive' => true])->toArray(),
                ServicePaymentMethod::factory()->make(['isActive' => false])->toArray(),
            ],
        ])->toArray(),
    ])->toArray());
    $config = servicesConfig();
    $api = new Api($client, $config, new Url($config));

    $enabled = require __DIR__.'/samples/services-and-payment-methods/service.php';

    expect($enabled)->toHaveCount(1)
        ->and($client->lastRequest()?->url)->toEndWith('/merchant/merchant-1/service/service-1');
});

it('groups the channels for a checkout picker', function (): void {
    $client = new FakeClient(PaymentMethodsResponse::factory()->make()->http());
    $config = servicesConfig();
    $api = new Api($client, $config, new Url($config));

    $picker = require __DIR__.'/samples/services-and-payment-methods/payment-methods.php';

    expect($client->lastRequest()?->url)->toEndWith('/service/service-1/get-payment-methods')
        ->and($client->lastRequest()?->body)->toBe(['amount' => 4999, 'currency' => 'PLN'])
        ->and($picker)->toHaveKeys(['Płatność BLIK', 'Przelew tradycyjny'])
        ->and($picker['Przelew tradycyjny'][1])->toBe([
            'label' => 'Split Payment',
            'paymentMethod' => PaymentMethod::WIRE_TRANSFER,
            'paymentMethodCode' => PaymentMethodCode::WIRE_TRANSFER_SPLIT,
        ]);
});

it('tests against a fake client', function (): void {
    $config = servicesConfig();

    $client = require __DIR__.'/samples/services-and-payment-methods/testing.php';

    expect($client->sentCount())->toBe(1);
});
