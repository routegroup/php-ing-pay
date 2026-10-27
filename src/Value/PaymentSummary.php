<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\PaymentSummaryFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\TransactionStatus;

/**
 * @method static PaymentSummaryFactory factory()
 */
class PaymentSummary extends Payload
{
    use HasFactory;

    public readonly string $id;

    public readonly ?TransactionStatus $status;
}
