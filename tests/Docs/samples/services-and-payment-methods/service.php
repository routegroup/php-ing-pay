<?php

declare(strict_types=1);

/**
 * @var Api $api
 * @var Config $config
 */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Value\ServicePaymentMethod;

$service = $api->getService($config->serviceId)->service;

$enabled = array_filter(
    $service->paymentMethods,
    fn (ServicePaymentMethod $method): bool => $method->isActive === true,
);
// #endregion sample

return $enabled;
