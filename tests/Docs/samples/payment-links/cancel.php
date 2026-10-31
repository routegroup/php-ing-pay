<?php

declare(strict_types=1);

/**
 * @var Api $api
 * @var string $paymentId
 */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CancelPaymentRequest;

$cancelled = $api->cancelPayment(new CancelPaymentRequest(['paymentId' => $paymentId]));
// #endregion sample

return $cancelled;
