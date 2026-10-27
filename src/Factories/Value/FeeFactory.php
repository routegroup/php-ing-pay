<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\Fee;

/** @extends Factory<Fee> */
class FeeFactory extends Factory
{
    protected string $payload = Fee::class;

    protected function definition(): array
    {
        return [
            'merchant' => $this->faker()->numberBetween(10, 900),
            'surcharge' => $this->faker()->numberBetween(0, 300),
        ];
    }
}
