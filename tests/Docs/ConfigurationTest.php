<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs;

use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Types\Environment;
use ValueError;

/**
 * The samples on the Configuration page. The first reads the process
 * environment, so the test puts the values there and takes them away again.
 */
const CONFIGURATION_ENV = [
    'ING_PAY_MERCHANT_ID' => 'merchant-1',
    'ING_PAY_SERVICE_ID' => 'service-1',
    'ING_PAY_SERVICE_KEY' => 'key',
    'ING_PAY_API_KEY' => 'token-abc',
    'ING_PAY_ENV' => 'sandbox',
];

afterEach(function (): void {
    foreach (array_keys(CONFIGURATION_ENV) as $name) {
        putenv($name);
    }
});

it('builds the config from the environment', function (): void {
    foreach (CONFIGURATION_ENV as $name => $value) {
        putenv("{$name}={$value}");
    }

    $config = require __DIR__.'/samples/configuration/config.php';

    expect($config)
        ->merchantId->toBe('merchant-1')
        ->serviceId->toBe('service-1')
        ->serviceKey->toBe('key')
        ->apiKey->toBe('token-abc')
        ->env->toBe(Environment::SANDBOX);
});

it('refuses an environment it does not know', function (): void {
    putenv('ING_PAY_ENV=staging');

    expect(fn (): mixed => require __DIR__.'/samples/configuration/config.php')->toThrow(ValueError::class);
});

it('builds a config for tests from made-up values', function (): void {
    $config = require __DIR__.'/samples/configuration/testing.php';

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->env)->toBe(Environment::SANDBOX);
});
