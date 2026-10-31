<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\ApiErrorResponse;
use Routegroup\IngPay\Response\TransactionResponse;

$client = new FakeClient()
    ->queue(TransactionResponse::factory()->make()->toArray())
    ->queue(ApiErrorResponse::factory()->make()->toArray(), 422);

$api = new Api($client, $config, new Url($config));

$api->createTransaction(CreateTransactionRequest::factory()->make());

expect(fn (): TransactionResponse => $api->createTransaction(CreateTransactionRequest::factory()->make()))
    ->toThrow(ApiErrorException::class)
    ->and($client->sent())->toHaveCount(2);
// #endregion sample

return $client;
