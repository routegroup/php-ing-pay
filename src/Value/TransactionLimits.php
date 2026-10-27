<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\TransactionLimitsFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static TransactionLimitsFactory factory()
 */
class TransactionLimits extends Payload
{
    use HasFactory;

    public readonly ?TransactionLimit $minTransaction;

    public readonly ?TransactionLimit $maxTransaction;
}
