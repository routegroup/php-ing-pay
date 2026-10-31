<?php

declare(strict_types=1);

/**
 * @var Api $api
 * @var string $transactionId
 */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Types\TransactionStatus;

$transaction = $api->getTransaction($transactionId)->transaction;

if ($transaction->status === TransactionStatus::SETTLED) {
    // the money is in; fulfil the order
}
// #endregion sample

return $transaction;
