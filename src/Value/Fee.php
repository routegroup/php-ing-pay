<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\FeeFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static FeeFactory factory()
 */
class Fee extends Payload
{
    use HasFactory;

    public readonly ?int $merchant;

    public readonly ?int $surcharge;
}
