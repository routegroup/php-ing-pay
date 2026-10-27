<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Notification;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Notification\GenericNotificationFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Value\Action;
use Routegroup\IngPay\Value\PaymentProfile;
use Routegroup\IngPay\Value\Transaction;
use Routegroup\IngPay\Value\TransactionPayment;

/**
 * @method static GenericNotificationFactory factory()
 */
class GenericNotification extends Payload
{
    use HasFactory;

    public readonly ?Transaction $transaction;

    public readonly ?TransactionPayment $payment;

    public readonly ?Action $action;

    public readonly ?PaymentProfile $paymentProfile;
}
