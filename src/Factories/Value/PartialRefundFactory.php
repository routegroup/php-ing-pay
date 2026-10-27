<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\PartialRefund;

/** @extends Factory<PartialRefund> */
class PartialRefundFactory extends Factory
{
    protected string $payload = PartialRefund::class;

    protected function definition(): array
    {
        $min = $this->faker()->numberBetween(100, 5000);

        return [
            'minRefundAmount' => $min,
            'maxRefundAmount' => $min + $this->faker()->numberBetween(0, 100000),
        ];
    }
}
