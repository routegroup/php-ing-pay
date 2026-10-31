<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Types\ActionType;
use Routegroup\IngPay\Types\Environment;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Transaction;

/**
 * The samples on the Transactions page, run in the order the page shows them,
 * so a sample that continues from the one above it sees its variables.
 */
function docsConfig(): Config
{
    return new Config(
        merchantId: 'merchant-1',
        serviceId: 'service-1',
        serviceKey: 'key',
        apiKey: 'token-abc',
        env: Environment::SANDBOX,
    );
}

function docsApi(FakeClient $client): Api
{
    return new Api($client, docsConfig(), new Url(docsConfig()));
}

it('creates a transaction and sends the payer where the gateway says', function (): void {
    $client = FakeClient::answering(TransactionResponse::factory()->state([
        'action' => ['type' => ActionType::REDIRECT, 'url' => 'https://paywall.example/go'],
    ])->make()->toArray());
    $api = docsApi($client);

    $response = require __DIR__.'/samples/transactions/create.php';

    expect($client->lastRequest()?->body)
        ->toMatchArray(['amount' => 4999, 'orderId' => 'ORDER-1042', 'paymentMethod' => 'pbl'])
        ->toHaveKey('serviceId', 'service-1');

    $next = require __DIR__.'/samples/transactions/action.php';

    expect($next)->toBe(['redirect', 'https://paywall.example/go']);
});

it('waits rather than redirects when blik was paid with a code', function (): void {
    $client = FakeClient::answering(TransactionResponse::factory()->withoutAction()->make()->toArray());
    $api = docsApi($client);

    $response = require __DIR__.'/samples/transactions/blik-code.php';

    expect($client->lastRequest()?->body)->toMatchArray(['blikCode' => '777123']);

    $next = require __DIR__.'/samples/transactions/action.php';

    expect($next)->toBe(['wait', null]);
});

it('reads a transaction back by its id', function (): void {
    $client = FakeClient::answering(TransactionResponse::factory()->state([
        'transaction' => Transaction::factory()->make(['id' => 'tx-1', 'status' => TransactionStatus::SETTLED]),
    ])->make()->toArray());
    $api = docsApi($client);

    require __DIR__.'/samples/transactions/create.php';
    $transaction = require __DIR__.'/samples/transactions/read.php';

    expect($client->lastRequest()?->url)->toEndWith('/transaction/tx-1')
        ->and($transaction->status)->toBe(TransactionStatus::SETTLED);
});

it('tests against a fake client', function (): void {
    $config = docsConfig();

    $client = require __DIR__.'/samples/transactions/testing.php';

    expect($client->sentCount())->toBe(1);
});
