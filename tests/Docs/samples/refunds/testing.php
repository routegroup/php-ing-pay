<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\RefundRequest;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\RefundResponse;

$client = FakeClient::answering(
    RefundResponse::factory()->make()->toArray(),
);

$api = new Api($client, $config, new Url($config));

$api->createRefund(new RefundRequest(['amount' => 2000]), 'tx-1');

expect($client->lastRequest()?->url)->toEndWith('/transaction/tx-1/refund')
    ->and($client->lastRequest()?->body['amount'])->toBe(2000);
// #endregion sample

return $client;
