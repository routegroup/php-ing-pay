<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Types\Environment;

/** The samples on the Testing page. Each one is a test body in its own right. */
function testingConfig(): Config
{
    return new Config(
        merchantId: 'merchant-1',
        serviceId: 'service-1',
        serviceKey: 'key',
        apiKey: 'token-abc',
        env: Environment::SANDBOX,
    );
}

afterEach(function (): void {
    Factory::flushSharedState();
});

it('fakes the gateway and asserts what was sent', function (): void {
    $config = testingConfig();

    $client = require __DIR__.'/samples/testing/fake.php';

    expect($client->sentCount())->toBe(1);
});

it('queues answers in order, a refusal among them', function (): void {
    $config = testingConfig();

    $client = require __DIR__.'/samples/testing/queue.php';

    expect($client->sentCount())->toBe(2);
});

it('bakes the configuration into every factory', function (): void {
    $config = testingConfig();

    require __DIR__.'/samples/testing/shared-state.php';
});
