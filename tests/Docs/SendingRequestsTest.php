<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\GetTransactionResponse;
use Routegroup\IngPay\Value\Transaction;

/** The samples on the Sending Requests page. Nothing here reaches a network. */
it('builds an api over guzzle without sending anything', function (): void {
    $config = docsConfig();

    $api = require __DIR__.'/samples/sending-requests/api.php';

    expect($api)->toBeInstanceOf(Api::class)
        ->and($api->url->config)->toBe($config);
});

it('reads a transaction when the gateway has it', function (): void {
    $api = docsApi(FakeClient::answering(GetTransactionResponse::factory()->make()->toArray()));

    $transaction = require __DIR__.'/samples/sending-requests/errors.php';

    expect($transaction)->toBeInstanceOf(Transaction::class);
});

it('treats an unknown transaction as none', function (): void {
    $api = docsApi(FakeClient::answering(status: 404));

    $transaction = require __DIR__.'/samples/sending-requests/errors.php';

    expect($transaction)->toBeNull();
});

it('rethrows any other refusal', function (): void {
    $api = docsApi(FakeClient::answering(['message' => 'Internal error'], 500));

    require __DIR__.'/samples/sending-requests/errors.php';
})->throws(ApiErrorException::class, 'Internal error');

it('tests against queued answers', function (): void {
    $config = docsConfig();

    $client = require __DIR__.'/samples/sending-requests/testing.php';

    expect($client->sent())->toHaveCount(2);
});
