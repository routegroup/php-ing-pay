<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\InvoiceBuyerType;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Types\TaxStake;
use Routegroup\IngPay\Value\Invoice;

$invoice = new Invoice([
    'buyer' => [
        'type' => InvoiceBuyerType::PERSON,
        'email' => 'anna@example.com',
        'fullName' => 'Anna Kowalska',
        'street' => 'Prosta 1',
        'city' => 'Warszawa',
        'postalCode' => '00-001',
        'countryCodeAlpha2' => 'PL',
    ],
    'positions' => [[
        'name' => 'Laptop 14"',
        'code' => 'SKU-1',
        'quantity' => 1,
        'unit' => 'szt.',
        'grossAmount' => 369000,
        'taxStake' => TaxStake::TAX_23,
    ]],
]);

$response = $api->createTransaction(new CreateTransactionRequest([
    'amount' => 369000,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1046',
    'paymentMethod' => PaymentMethod::PAY_BY_LINK,
    'paymentMethodCode' => PaymentMethodCode::ING,
    'successReturnUrl' => 'https://shop.example/orders/1046/paid',
    'failureReturnUrl' => 'https://shop.example/orders/1046/failed',
    'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska', 'email' => 'anna@example.com'],
    'invoice' => $invoice,
]));
// #endregion sample

return $invoice;
