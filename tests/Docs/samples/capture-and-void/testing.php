<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\TransactionResponse;

$client = FakeClient::answering(
    TransactionResponse::factory()->make()->toArray(),
);

$api = new Api($client, $config, new Url($config));

$api->captureTransaction('tx-1');

expect($client->lastRequest()?->method)->toBe('POST')
    ->and($client->lastRequest()?->url)->toEndWith('/transaction/tx-1/capture');
// #endregion sample

return $client;
