<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Transaction;

$transaction = Transaction::factory()->make();

$settled = Transaction::factory()
    ->state(['status' => TransactionStatus::SETTLED])
    ->make(['amount' => 4999]);

$history = Transaction::factory()
    ->sequence(['status' => TransactionStatus::PENDING], ['status' => TransactionStatus::SETTLED])
    ->makeMany(4);
// #endregion sample

return [$transaction, $settled, $history];
