<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;
use Routegroup\IngPay\Types\Currency;

$paywall = new Paywall($config, new Utils($config));

$request = new CreateTransactionRequest([
    'amount' => 4999,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1042',
    'customerFirstName' => 'Anna',
    'customerLastName' => 'Kowalska',
    'customerEmail' => 'anna@example.com',
    'urlSuccess' => 'https://shop.example/orders/1042/paid',
    'urlFailure' => 'https://shop.example/orders/1042/failed',
]);

$url = $paywall->url($request);

header('Location: '.$url, true, 303);
// #endregion sample

return $url;
