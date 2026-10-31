<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Types\PaymentMethod;

$request = new CreateTransactionRequest([
    'amount' => 4999,
    'currency' => 'PLN',
    'orderId' => 'ORDER-1042',
    'paymentMethod' => PaymentMethod::PAY_BY_LINK,
    'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska'],
]);

$json = json_encode($request, JSON_THROW_ON_ERROR);
// #endregion sample

return [$request, $json];
