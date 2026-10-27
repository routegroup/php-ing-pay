<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\BaseResponse;
use Routegroup\IngPay\Response\GetTransactionResponse;
use Routegroup\IngPay\Response\ProfileResponse;
use Routegroup\IngPay\Response\ProfilesResponse;
use Routegroup\IngPay\Response\ServiceResponse;
use Routegroup\IngPay\Response\ServicesResponse;
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Response\TrustedIpsResponse;
use Routegroup\IngPay\Types\Environment;
use RuntimeException;

function apiConfig(bool $strict = true): Config
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

/**
 * A body the given response can actually hydrate, built by its own factory, so
 * a test about the request does not fail on the answer.
 *
 * @param  class-string<BaseResponse>  $response
 * @return array<string, mixed>
 */
function bodyFor(string $response): array
{
    $built = $response::factory()->make()->toArray();

    return $response === ServicesResponse::class ? [] : $built;
}

/** @return array{Api, FakeClient} */
function api(FakeClient $client, bool $strict = true): array
{
    $config = apiConfig($strict);

    return [new Api($client, $config, new Url($config)), $client];
}

it('authorises every call with the api key', function (): void {
    [$api, $client] = api(FakeClient::answering([]));

    $api->getServices();

    expect($client->lastRequest()?->headers)->toBe(['Authorization' => 'Bearer token-abc']);
});

it('builds the url from the configured environment and merchant', function (): void {
    [$api, $client] = api(FakeClient::answering(bodyFor(GetTransactionResponse::class)));

    $api->getTransaction('tx-1');

    expect($client->lastRequest()?->url)
        ->toBe('https://api.sandbox.pay.ing.pl/v1/merchant/merchant-1/transaction/tx-1')
        ->and($client->lastRequest()?->method)->toBe('GET');
});

it('sends the verb each endpoint expects', function (string $method, array $args, string $verb, string $response): void {
    [$api, $client] = api(FakeClient::answering(bodyFor($response)));

    $api->{$method}(...$args);

    expect($client->lastRequest()?->method)->toBe($verb);
})->with([
    ['getServices', [], 'GET', ServicesResponse::class],
    ['getService', ['svc-1'], 'GET', ServiceResponse::class],
    ['getTrustedIps', [], 'GET', TrustedIpsResponse::class],
    ['getProfile', ['p-1'], 'GET', ProfileResponse::class],
    ['getProfilesByCid', ['cid-1'], 'GET', ProfilesResponse::class],
    ['deactivateProfile', ['p-1'], 'DELETE', ProfileResponse::class],
    ['captureTransaction', ['tx-1'], 'POST', TransactionResponse::class],
    ['voidTransaction', ['tx-1'], 'POST', TransactionResponse::class],
]);

it('fills the caller identity into a request on the way out', function (): void {
    [$api, $client] = api(FakeClient::answering(bodyFor(TransactionResponse::class)));

    $api->createTransaction(CreateTransactionRequest::factory()->make(['serviceId' => null]));

    expect($client->lastRequest()?->body['serviceId'])->toBe('service-1');
});

it('leaves a service the caller chose alone', function (): void {
    [$api, $client] = api(FakeClient::answering(bodyFor(TransactionResponse::class)));

    $api->createTransaction(CreateTransactionRequest::factory()->make(['serviceId' => 'chosen']));

    expect($client->lastRequest()?->body['serviceId'])->toBe('chosen');
});

it('does not reach for configuration until the request is sent', function (): void {
    $request = CreateTransactionRequest::factory()->make(['serviceId' => null]);

    expect($request->toArray())->not->toHaveKey('serviceId');
});

it('hands back a typed response carrying the answer', function (): void {
    [$api] = api(FakeClient::answering(['transaction' => ['id' => 'tx-1']], 201));

    $response = $api->getServices();

    expect($response->successful())->toBeTrue()
        ->and($response->http()->status)->toBe(201);
});

it('raises on a refusal, naming the status and the message', function (): void {
    [$api] = api(FakeClient::answering(['apiErrorResponse' => ['message' => 'nope', 'code' => '422']], 422));

    expect(fn () => $api->getServices())
        ->toThrow(ApiErrorException::class, 'nope');
});

it('carries the request and the error detail for logging', function (): void {
    [$api] = api(FakeClient::answering(['apiErrorResponse' => ['message' => 'bad amount']], 422));

    try {
        $api->createTransaction(CreateTransactionRequest::factory()->make(['serviceId' => null]));
    } catch (ApiErrorException $exception) {
        expect($exception->status())->toBe(422)
            ->and($exception->error->message)->toBe('bad amount')
            ->and($exception->context()['request'])->toHaveKey('serviceId')
            ->and($exception->context()['request']['serviceId'])->toBe('service-1');

        return;
    }

    throw new RuntimeException('ApiErrorException was not thrown.');
});

it('always raises on 422 and 500, flag or no flag', function (int $status): void {
    [$api] = api(FakeClient::answering([], $status), strict: false);

    expect(fn () => $api->getServices())->toThrow(ApiErrorException::class);
})->with([422, 500]);

it('stays quiet on other refusals when strict errors are off', function (int $status): void {
    [$api] = api(FakeClient::answering([], $status), strict: false);

    expect($api->getServices()->failed())->toBeTrue();
})->with([400, 401, 404, 409, 503]);

it('raises on any refusal when strict errors are on', function (int $status): void {
    [$api] = api(FakeClient::answering([], $status));

    expect(fn () => $api->getServices())->toThrow(ApiErrorException::class);
})->with([400, 401, 404, 409, 503]);
