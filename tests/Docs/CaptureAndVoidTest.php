<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs;

use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\GetTransactionResponse;
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Transaction;

/**
 * The samples on the Capture & Void page.
 *
 * @param  class-string<TransactionResponse|GetTransactionResponse>  $class
 * @return array<string, mixed>
 */
function answeringWith(TransactionStatus $status, string $class = TransactionResponse::class): array
{
    return $class::factory()->make([
        'transaction' => Transaction::factory()->make(['status' => $status]),
    ])->toArray();
}

it('captures a transaction that is authorised', function (): void {
    $client = new FakeClient()->queue(answeringWith(TransactionStatus::AUTHORIZED, GetTransactionResponse::class))
        ->queue(answeringWith(TransactionStatus::SETTLED));
    $api = docsApi($client);

    $transaction = require __DIR__.'/samples/capture-and-void/capture.php';

    expect($client->sentCount())->toBe(2)
        ->and($client->lastRequest()?->url)->toEndWith('/transaction/79333e36-883a-4566-a84e-97c933b37e19/capture')
        ->and($transaction->status)->toBe(TransactionStatus::SETTLED);
});

it('leaves a transaction alone that is not authorised', function (): void {
    $client = FakeClient::answering(answeringWith(TransactionStatus::SETTLED, GetTransactionResponse::class));
    $api = docsApi($client);

    require __DIR__.'/samples/capture-and-void/capture.php';

    expect($client->sentCount())->toBe(1);
});

it('voids a transaction', function (): void {
    $client = FakeClient::answering(answeringWith(TransactionStatus::CANCELLED));
    $api = docsApi($client);

    require __DIR__.'/samples/capture-and-void/void.php';

    expect($client->lastRequest()?->method)->toBe('POST')
        ->and($client->lastRequest()?->url)->toEndWith('/transaction/79333e36-883a-4566-a84e-97c933b37e19/void');
});

it('tests a capture against a fake client', function (): void {
    $config = docsConfig();

    $client = require __DIR__.'/samples/capture-and-void/testing.php';

    expect($client->sentCount())->toBe(1);
});
