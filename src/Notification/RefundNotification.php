<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Notification;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Notification\RefundNotificationFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Value\Transaction;
use Routegroup\IngPay\Value\TransactionPayment;

/**
 * @method static RefundNotificationFactory factory()
 */
class RefundNotification extends Payload
{
    use HasFactory;

    public readonly Transaction $transaction;

    public readonly TransactionPayment $payment;

    public readonly string $originalTransactionId;
}
