<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Sandbox;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Http\Psr18Client;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
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
