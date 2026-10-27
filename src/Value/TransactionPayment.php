<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\TransactionPaymentFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\TransactionStatus;

/**
 * @method static TransactionPaymentFactory factory()
 */
class TransactionPayment extends Payload
{
    use HasFactory;

    public readonly string $id;

    public readonly ?string $title;

    public readonly int $amount;

    public readonly ?TransactionStatus $status;

    public readonly int $created;

    public readonly string $orderId;

    public readonly ?Currency $currency;

    public readonly int $modified;

    public readonly string $serviceId;
}
