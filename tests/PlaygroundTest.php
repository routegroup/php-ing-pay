<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\Psr18Client;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Types\Environment;

/**
 * A scratch file for probing the real sandbox. Nothing here runs unless
 * PLAYGROUND is set, so it stays out of the way of the suite; put a call in the
 * body, run it, and throw the body away afterwards.
 *
 *   PLAYGROUND=1 docker compose run --rm app vendor/bin/pest tests/PlaygroundTest.php
 *
 * Credentials come from a .env this package never commits. See .env.example.
 */
function playgroundEnv(string $key): ?string
{
    static $file = null;

    if ($file === null) {
        $file = [];
        $path = __DIR__.'/../.env';

        foreach (is_readable($path) ? (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []) : [] as $line) {
            if (str_starts_with(trim($line), '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $file[trim($name)] = trim($value, " \t\"'");
        }
    }

    /** @var mixed $value */
    $value = $file[$key] ?? $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    return is_string($value) && $value !== '' ? $value : null;
}

function playgroundApi(): Api
{
    $config = new Config(
        merchantId: playgroundEnv('ING_PAY_MERCHANT_ID') ?? '',
        serviceId: playgroundEnv('ING_PAY_SERVICE_ID') ?? '',
        serviceKey: playgroundEnv('ING_PAY_SERVICE_KEY') ?? '',
        apiKey: playgroundEnv('ING_PAY_API_KEY') ?? '',
        env: Environment::from(playgroundEnv('ING_PAY_ENV') ?? Environment::SANDBOX->value),
    );

    return new Api(
        Psr18Client::guzzle([
            'timeout' => $config->timeout,
            'connect_timeout' => $config->connectTimeout,
            'http_errors' => false,
        ]),
        $config,
        new Url($config),
    );
}

it('is for playground purposes', function (): void {
    $api = playgroundApi();
    // Read from the process rather than from .env: the credentials file lives
    // here permanently now, so a PLAYGROUND line in it would quietly point the
    // ordinary test run at the live gateway.
})->skip(fn (): bool => getenv('PLAYGROUND') === false, 'playground disabled');
