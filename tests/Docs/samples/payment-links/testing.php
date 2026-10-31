<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreatePaymentRequest;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\PaymentResponse;

$client = FakeClient::answering(
    PaymentResponse::factory()->make()->toArray(),
);

$api = new Api($client, $config, new Url($config));

$api->createPayment(CreatePaymentRequest::factory()->make(['orderId' => 'ORDER-1042']));

expect($client->lastRequest()?->body['orderId'])->toBe('ORDER-1042');
// #endregion sample

return $client;
