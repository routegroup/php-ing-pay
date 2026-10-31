<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\GetTransactionResponse;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\Environment;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Customer;
use Routegroup\IngPay\Value\Fee;
use Routegroup\IngPay\Value\Transaction;

/** The samples on the Payloads page. */
it('builds a request from arrays and enums and serialises it back', function (): void {
    /** @var CreateTransactionRequest $request */
    [$request, $json] = require __DIR__.'/samples/payloads/build.php';

    expect($request->currency)->toBe(Currency::PLN)
        ->and($request->customer)->toBeInstanceOf(Customer::class)
        ->and($request->customer?->email)->toBeNull()
        ->and(json_decode((string) $json, true))->toBe([
            'type' => 'sale',
            'amount' => 4999,
            'currency' => 'PLN',
            'orderId' => 'ORDER-1042',
            'paymentMethod' => 'pbl',
            'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska'],
        ]);
});

it('reads typed values off a response', function (): void {
    $client = FakeClient::answering(GetTransactionResponse::factory()->make([
        'transaction' => Transaction::factory()->make([
            'status' => TransactionStatus::SETTLED,
            'fee' => new Fee(['merchant' => 120]),
        ]),
    ])->toArray());
    $config = new Config('merchant-1', 'service-1', 'key', 'token-abc', Environment::SANDBOX);
    $api = new Api($client, $config, new Url($config));

    [$settled, $fee, $answer] = require __DIR__.'/samples/payloads/read.php';

    expect($settled)->toBeTrue()
        ->and($fee)->toBe(120)
        ->and($answer)->toBeJson();
});

it('builds payloads with factories', function (): void {
    /** @var list<Transaction> $history */
    [$transaction, $settled, $history] = require __DIR__.'/samples/payloads/factories.php';

    expect($transaction)->toBeInstanceOf(Transaction::class)
        ->and($settled->status)->toBe(TransactionStatus::SETTLED)
        ->and($settled->amount)->toBe(4999)
        ->and(array_map(fn (Transaction $item): ?TransactionStatus => $item->status, $history))->toBe([
            TransactionStatus::PENDING,
            TransactionStatus::SETTLED,
            TransactionStatus::PENDING,
            TransactionStatus::SETTLED,
        ]);
});
