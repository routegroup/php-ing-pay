<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Value\Shipping;

class ShippingFactory extends BillingFactory
{
    protected string $payload = Shipping::class;

    protected function definition(): array
    {
        $attributes = parent::definition();
        unset($attributes['taxId']);

        return $attributes;
    }
}
