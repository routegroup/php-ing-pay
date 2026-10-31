<?php

declare(strict_types=1);

/**
 * @var Api $api
 * @var string $paymentId
 */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Types\TransactionStatus;

$payment = $api->getPayment($paymentId);

if ($payment->status === TransactionStatus::SETTLED) {
    // the link was paid; fulfil the order
}
// #endregion sample

return $payment;
