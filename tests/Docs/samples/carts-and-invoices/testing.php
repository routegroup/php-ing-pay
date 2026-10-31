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
use Routegroup\IngPay\Value\Cart;

$client = FakeClient::answering(
    PaymentResponse::factory()->make()->toArray(),
);

$api = new Api($client, $config, new Url($config));

$cart = Cart::factory()->make();

$api->createPayment(CreatePaymentRequest::factory()->make(['cart' => $cart]));

expect($client->lastRequest()?->body['cart'])->toBe($cart->toArray());
// #endregion sample

return $client;
