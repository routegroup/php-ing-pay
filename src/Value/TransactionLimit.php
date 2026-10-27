<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\TransactionLimitFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static TransactionLimitFactory factory()
 */
class TransactionLimit extends Payload
{
    use HasFactory;

    public readonly ?string $type;

    public readonly ?int $value;
}
