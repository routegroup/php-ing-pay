<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\ShippingFactory;

/**
 * @method static ShippingFactory factory()
 */
class Shipping extends Billing
{
    use HasFactory;

    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;
        unset($attributes['taxId']);

        parent::__construct($attributes);
    }
}
