<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\Psr18Client;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;

$api = new Api(
    Psr18Client::guzzle(['timeout' => 30, 'connect_timeout' => 10]),
    $config,
    new Url($config),
);
// #endregion sample

return $api;
