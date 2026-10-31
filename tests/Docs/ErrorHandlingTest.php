<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Types\Environment;

/** The samples on the Error Handling page. */
function errorsConfig(bool $strict = true): Config
{
    return new Config(
        merchantId: 'merchant-1',
        serviceId: 'service-1',
        serviceKey: 'key',
        apiKey: 'token-abc',
        env: Environment::SANDBOX,
        strictErrors: $strict,
    );
}

it('catches a refusal and reads what the gateway said', function (): void {
    $client = FakeClient::answering(['apiErrorResponse' => [
        'code' => '404',
        'message' => 'Transaction not found',
        'errors' => [['property' => 'transactionId', 'message' => 'does not exist']],
    ]], 404);
    $api = new Api($client, errorsConfig(), new Url(errorsConfig()));

    [$status, $message, $problems, $log] = require __DIR__.'/samples/error-handling/catch.php';

    expect($status)->toBe(404)
        ->and($message)->toBe('Transaction not found')
        ->and($problems)->toBe(['transactionId: does not exist'])
        ->and($log)->toHaveKeys(['status', 'response', 'request'])
        ->and($log['request'])->toBe(['transactionId' => '5b8d0c4e-1f7a-4c2e-9e3b-2a6f1d9c8e70']);
});

it('reads a refusal off the response when strict errors are off', function (): void {
    $client = FakeClient::answering(['message' => 'Not found'], 404);
    $api = new Api($client, errorsConfig(strict: false), new Url(errorsConfig(strict: false)));

    [$status, $message] = require __DIR__.'/samples/error-handling/lenient.php';

    expect($status)->toBe(404)
        ->and($message)->toBe('Not found');
});

it('tests a refusal against a fake client', function (): void {
    $config = errorsConfig();

    $client = require __DIR__.'/samples/error-handling/testing.php';

    expect($client->sentCount())->toBe(1);
});
