<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\ServiceResponse;

$client = FakeClient::answering(ServiceResponse::factory()->make()->toArray());

$api = new Api($client, $config, new Url($config));

expect($api->getService($config->serviceId)->service->paymentMethods)->not->toBeEmpty()
    ->and($client->lastRequest()?->url)->toEndWith("/service/{$config->serviceId}");
// #endregion sample

return $client;
