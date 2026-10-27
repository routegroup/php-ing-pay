<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\PartialRefundFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static PartialRefundFactory factory()
 */
class PartialRefund extends Payload
{
    use HasFactory;

    public readonly int $maxRefundAmount;

    public readonly int $minRefundAmount;
}
