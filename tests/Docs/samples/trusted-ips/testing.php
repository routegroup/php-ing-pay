<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\PutTrustedIpsRequest;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\TrustedIpsResponse;

$client = FakeClient::answering(TrustedIpsResponse::factory()->afterPut()->make()->toArray());

$api = new Api($client, $config, new Url($config));

$api->putTrustedIps(new PutTrustedIpsRequest(['trustedIps' => ['203.0.113.10']]));

expect($client->lastRequest()?->method)->toBe('PUT')
    ->and($client->lastRequest()?->body['trustedIps'])->toBe(['203.0.113.10']);
// #endregion sample

return $client;
