<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\TrustedIpsResponse;
use Routegroup\IngPay\Types\Environment;

/** The samples on the Trusted IPs page, run in the order the page shows them. */
function trustedIpsConfig(): Config
{
    return new Config(
        merchantId: 'merchant-1',
        serviceId: 'service-1',
        serviceKey: 'key',
        apiKey: 'token-abc',
        env: Environment::SANDBOX,
    );
}

it('reads the list and sends it back with one more address', function (): void {
    $client = new FakeClient()
        ->queue(TrustedIpsResponse::factory()->make(['trustedIps' => ['198.51.100.7']])->toArray())
        ->queue(TrustedIpsResponse::factory()->afterPut()->make()->toArray());
    $api = new Api($client, trustedIpsConfig(), new Url(trustedIpsConfig()));

    $trusted = require __DIR__.'/samples/trusted-ips/read.php';

    expect($trusted)->toBe(['198.51.100.7'])
        ->and($client->lastRequest()?->url)->toEndWith('/merchant/merchant-1/settings/ips');

    $saved = require __DIR__.'/samples/trusted-ips/update.php';

    expect($saved)->toBeTrue()
        ->and($client->lastRequest()?->method)->toBe('PUT')
        ->and($client->lastRequest()?->body)->toBe(['trustedIps' => ['198.51.100.7', '203.0.113.10']]);
});

it('tests against a fake client', function (): void {
    $config = trustedIpsConfig();

    $client = require __DIR__.'/samples/trusted-ips/testing.php';

    expect($client->sentCount())->toBe(1);
});
