<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Sandbox;

use Routegroup\IngPay\Api\GetPaymentMethodsRequest;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Value\PaymentMethodChannel;
use Routegroup\IngPay\Value\PaymentMethodGroup;
use Routegroup\IngPay\Value\ServiceData;
use Routegroup\IngPay\Value\ServicePaymentMethod;

it('lists the services the merchant has', function (): void {
    $services = Sandbox::api()->getServices()->services ?? [];

    expect($services)->not->toBeEmpty()
        ->and($services[0])->toBeInstanceOf(ServiceData::class)
        ->and($services[0]->id)->not->toBeEmpty();
})->skip(fn (): bool => ! Sandbox::isConfigured(), Sandbox::NO_CREDENTIALS);

it('reads back the service it is configured for', function (): void {
    $config = Sandbox::config();

    $service = Sandbox::api()->getService($config->serviceId)->service;

    expect($service->id)->toBe($config->serviceId)
        ->and($service->paymentMethods[0] ?? null)->toBeInstanceOf(ServicePaymentMethod::class);
})->skip(fn (): bool => ! Sandbox::isConfigured(), Sandbox::NO_CREDENTIALS);

it('lists payment methods as objects, down to the channels', function (): void {
    $methods = Sandbox::api()->getPaymentMethods(
        new GetPaymentMethodsRequest(['amount' => 1000, 'currency' => Currency::PLN]),
        Sandbox::config()->serviceId,
    )->methods ?? [];

    expect($methods)->not->toBeEmpty()
        ->and($methods[0])->toBeInstanceOf(PaymentMethodGroup::class)
        ->and($methods[0]->channels[0] ?? null)->toBeInstanceOf(PaymentMethodChannel::class);
})->skip(fn (): bool => ! Sandbox::isConfigured(), Sandbox::NO_CREDENTIALS);

it('answers which addresses the merchant trusts', function (): void {
    $response = Sandbox::api()->getTrustedIps();

    expect($response->http()->status)->toBe(200)
        ->and($response->trustedIps)->toBeArray();
})->skip(fn (): bool => ! Sandbox::isConfigured(), Sandbox::NO_CREDENTIALS);
