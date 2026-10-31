<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;

$response = $api->createTransaction(new CreateTransactionRequest([
    'amount' => 4999,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1043',
    'paymentMethod' => PaymentMethod::BLIK,
    'paymentMethodCode' => PaymentMethodCode::BLIK,
    'blikCode' => '777123',
    'successReturnUrl' => 'https://shop.example/orders/1043/paid',
    'failureReturnUrl' => 'https://shop.example/orders/1043/failed',
    'customer' => [
        'firstName' => 'Anna',
        'lastName' => 'Kowalska',
        'email' => 'anna@example.com',
    ],
]));
// #endregion sample

return $response;
