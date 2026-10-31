<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\OneClickRequest;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\Lang;

$paywall = new Paywall($config, new Utils($config));

$fields = $paywall->fields(new OneClickRequest([
    'amount' => 4999,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1042',
    'customerId' => 'customer-17',
    'customerFirstName' => 'Anna',
    'customerLastName' => 'Kowalska',
    'customerEmail' => 'anna@example.com',
    'urlSuccess' => 'https://shop.example/orders/1042/paid',
    'urlFailure' => 'https://shop.example/orders/1042/failed',
    'urlReturn' => 'https://shop.example/checkout',
    'locale' => Lang::PL,
]));

$attributes = '';

foreach ($fields as $name => $value) {
    $attribute = strtolower((string) preg_replace('/(?<!^)[A-Z]/', '-$0', $name));
    $attributes .= sprintf(' data-%s="%s"', $attribute, htmlspecialchars((string) $value));
}

echo '<div id="imoje-widget__wrapper"></div>'
    .'<script src="'.$config->env->widgetUrl().'" id="'.OneClickRequest::SCRIPT_ID.'"'.$attributes.'></script>';
// #endregion sample

return $fields;
