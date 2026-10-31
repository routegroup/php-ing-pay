<?php

declare(strict_types=1);

/**
 * @var Validator $validator
 * @var string $body
 */

// #region sample
use Routegroup\IngPay\Lib\Validator;
use Routegroup\IngPay\Notification\GenericNotification;
use Routegroup\IngPay\Notification\RefundNotification;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Types\TransactionType;

$payload = json_decode($body, true, flags: JSON_THROW_ON_ERROR);

$validator->fromNotification($payload);

$notification = ($payload['transaction']['type'] ?? null) === TransactionType::REFUND->value
    ? new RefundNotification($payload)
    : new GenericNotification($payload);

if ($notification->transaction?->status === TransactionStatus::SETTLED) {
    // find the order by $notification->transaction->orderId and fulfil it
}

echo '{"status":"ok"}';
// #endregion sample

return $notification;
