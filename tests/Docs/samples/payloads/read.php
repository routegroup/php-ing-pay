<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Types\TransactionStatus;

$response = $api->getTransaction('5b8d0c4e-1f7a-4c2e-9e3b-2a6f1d9c8e70');
$transaction = $response->transaction;

$settled = $transaction->status === TransactionStatus::SETTLED;
$fee = $transaction->fee?->merchant;
$answer = $response->http()->body;
// #endregion sample

return [$settled, $fee, $answer];
