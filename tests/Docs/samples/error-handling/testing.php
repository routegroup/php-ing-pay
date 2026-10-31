<?php

declare(strict_types=1);

/** @var Config $config */
// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\ApiErrorResponse;
use Routegroup\IngPay\Response\GetTransactionResponse;

$client = FakeClient::answering(
    ApiErrorResponse::factory()->make(['message' => 'Transaction not found'])->toArray(),
    404,
);

$api = new Api($client, $config, new Url($config));

expect(fn (): GetTransactionResponse => $api->getTransaction('missing'))
    ->toThrow(ApiErrorException::class, 'Transaction not found');
// #endregion sample

return $client;
