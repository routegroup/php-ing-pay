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
    'amount' => 129900,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1044',
    'paymentMethod' => PaymentMethod::IMOJE_INSTALLMENTS,
    'paymentMethodCode' => PaymentMethodCode::INBANK,
    'installment' => ['period' => 12],
    'successReturnUrl' => 'https://shop.example/orders/1044/paid',
    'failureReturnUrl' => 'https://shop.example/orders/1044/failed',
    'customer' => [
        'firstName' => 'Anna',
        'lastName' => 'Kowalska',
        'email' => 'anna@example.com',
    ],
]));
// #endregion sample

return $response;
