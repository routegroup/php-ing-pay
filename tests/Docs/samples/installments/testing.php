<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Types\PaymentMethod;

$client = FakeClient::answering(
    TransactionResponse::factory()->make()->toArray(),
);

$api = new Api($client, $config, new Url($config));

$api->createTransaction(CreateTransactionRequest::factory()->make([
    'paymentMethod' => PaymentMethod::IMOJE_INSTALLMENTS,
    'installment' => ['period' => 12],
]));

expect($client->lastRequest()?->body['installment'])->toBe(['period' => 12]);
// #endregion sample

return $client;
