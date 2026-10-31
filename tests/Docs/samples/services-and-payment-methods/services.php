<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Value\ServiceData;

$services = $api->getServices()->services ?? [];

$active = array_filter($services, fn (ServiceData $service): bool => $service->isActive);
// #endregion sample

return $active;
