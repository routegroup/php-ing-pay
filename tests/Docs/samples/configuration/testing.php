<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Types\Environment;

$config = new Config(
    merchantId: 'merchant-test',
    serviceId: 'service-test',
    serviceKey: 'key-test',
    apiKey: 'token-test',
    env: Environment::SANDBOX,
);
// #endregion sample

return $config;
