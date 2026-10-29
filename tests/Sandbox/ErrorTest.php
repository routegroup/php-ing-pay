<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Sandbox;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Http\Psr18Client;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Types\TransactionType;
use RuntimeException;

function sandboxWith(string $apiKey): Api
{
    $sandbox = Sandbox::config();

    $config = new Config(
        merchantId: $sandbox->merchantId,
        serviceId: $sandbox->serviceId,
        serviceKey: $sandbox->serviceKey,
        apiKey: $apiKey,
        env: $sandbox->env,
    );

    return new Api(
        Psr18Client::guzzle(['timeout' => 30, 'connect_timeout' => 10, 'http_errors' => false]),
        $config,
        new Url($config),
    );
}

it('reads the envelope the gateway wraps an error in', function (): void {
    try {
        Sandbox::api()->getTransaction('00000000-0000-4000-8000-000000000000');
    } catch (ApiErrorException $exception) {
        expect($exception->status())->toBe(404)
            ->and($exception->error->code)->not->toBeEmpty()
            ->and($exception->error->message)->not->toBeEmpty();

        return;
    }

    throw new RuntimeException('the gateway answered a transaction that does not exist');
})->skip(fn (): bool => ! Sandbox::isConfigured(), Sandbox::NO_CREDENTIALS);

it('survives an error with no body at all', function (): void {
    try {
        sandboxWith('not-a-key')->getServices();
    } catch (ApiErrorException $exception) {
        expect($exception->status())->toBe(401)
            ->and($exception->response->body)->toBeEmpty()
            ->and($exception->error->code)->toBeNull();

        return;
    }

    throw new RuntimeException('the gateway accepted a key it should not have');
})->skip(fn (): bool => ! Sandbox::isConfigured(), Sandbox::NO_CREDENTIALS);

// The third reserved blik code never becomes a transaction at all: the
// provider error arrives as ACQ-ERROR-130003 on the way in, which is why
// TransactionStatus::ERROR stays out of reach here.
it('answers an error rather than a transaction for the failing blik code', function (): void {
    try {
        Sandbox::api()->createTransaction(new CreateTransactionRequest([
            'type' => TransactionType::SALE,
            'amount' => 1000,
            'currency' => Currency::PLN,
            'orderId' => Sandbox::orderId('CI-BLIKFAIL'),
            'paymentMethod' => PaymentMethod::BLIK,
            'paymentMethodCode' => PaymentMethodCode::BLIK,
            'blikCode' => '888222',
            'successReturnUrl' => 'https://example.test/success',
            'failureReturnUrl' => 'https://example.test/failure',
            'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska', 'email' => 'anna@example.test'],
        ]));
    } catch (ApiErrorException $exception) {
        expect($exception->status())->toBe(500)
            ->and($exception->error->code)->not->toBeEmpty();

        return;
    }

    throw new RuntimeException('the gateway accepted the blik code that simulates a provider error');
})->skip(fn (): bool => ! Sandbox::isConfigured(), Sandbox::NO_CREDENTIALS);
