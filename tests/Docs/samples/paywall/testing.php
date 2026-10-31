<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;

$paywall = new Paywall($config, new Utils($config));

$url = $paywall->url(CreateTransactionRequest::factory()->make(['orderId' => 'ORDER-1042']));

parse_str((string) parse_url($url, PHP_URL_QUERY), $fields);

expect($fields)
    ->toHaveKey('orderId', 'ORDER-1042')
    ->toHaveKey('signature');
// #endregion sample
