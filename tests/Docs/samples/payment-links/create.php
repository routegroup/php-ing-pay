<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreatePaymentRequest;
use Routegroup\IngPay\Types\Currency;

$response = $api->createPayment(new CreatePaymentRequest([
    'amount' => 4999,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1042',
    'title' => 'Order 1042',
    'customer' => [
        'firstName' => 'Anna',
        'lastName' => 'Kowalska',
        'email' => 'anna@example.com',
    ],
    'successReturnUrl' => 'https://shop.example/orders/1042/paid',
    'failureReturnUrl' => 'https://shop.example/orders/1042/failed',
]));

$paymentId = $response->payment->id;
$link = $response->payment->url;
// #endregion sample

return $response;
