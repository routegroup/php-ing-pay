<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Types\Environment;

$config = new Config(
    merchantId: (string) getenv('ING_PAY_MERCHANT_ID'),
    serviceId: (string) getenv('ING_PAY_SERVICE_ID'),
    serviceKey: (string) getenv('ING_PAY_SERVICE_KEY'),
    apiKey: (string) getenv('ING_PAY_API_KEY'),
    env: Environment::from((string) getenv('ING_PAY_ENV')),
);
// #endregion sample

return $config;
