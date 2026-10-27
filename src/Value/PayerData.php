<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\PayerDataFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static PayerDataFactory factory()
 */
class PayerData extends Payload
{
    use HasFactory;

    public readonly ?string $name;

    public readonly ?string $account;
}
