<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreatePaymentRequest;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\LeaseTaxStake;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Value\Cart;

$cart = new Cart(['items' => [[
    'id' => 'SKU-1',
    'name' => 'Laptop 14"',
    'amount' => 300000,
    'tax' => 69000,
    'taxStake' => LeaseTaxStake::RATE_23,
    'quantity' => 1,
    'url' => 'https://shop.example/products/sku-1',
    'categoryId' => 'laptops',
]]]);

$response = $api->createPayment(new CreatePaymentRequest([
    'amount' => 369000,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1045',
    'title' => 'Order 1045',
    'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska', 'email' => 'anna@example.com'],
    'preselectMethodCode' => PaymentMethodCode::LEASE_NOW,
    'cart' => $cart,
]));
// #endregion sample

return $cart;
