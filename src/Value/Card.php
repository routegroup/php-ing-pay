<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\CardFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static CardFactory factory()
 */
class Card extends Payload
{
    use HasFactory;

    public readonly string $firstName;

    public readonly string $lastName;

    public readonly string $number;

    public readonly string $month;

    public readonly string $year;

    public readonly string $cvv;
}
