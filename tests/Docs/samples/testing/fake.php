<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\GetTransactionResponse;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Transaction;

$client = FakeClient::answering(GetTransactionResponse::factory()->make([
    'transaction' => Transaction::factory()->make(['id' => 'tx-1', 'status' => TransactionStatus::SETTLED]),
])->toArray());

$api = new Api($client, $config, new Url($config));

expect($api->getTransaction('tx-1')->transaction->status)->toBe(TransactionStatus::SETTLED)
    ->and($client->lastRequest()?->method)->toBe('GET')
    ->and($client->lastRequest()?->url)->toEndWith('/transaction/tx-1');
// #endregion sample

return $client;
