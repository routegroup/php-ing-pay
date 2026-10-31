<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Value\Transaction;

Factory::useSharedState(fn (): array => [
    'merchantId' => $config->merchantId,
    'serviceId' => $config->serviceId,
]);

expect(Transaction::factory()->make()->serviceId)->toBe($config->serviceId);

Factory::flushSharedState();
// #endregion sample
