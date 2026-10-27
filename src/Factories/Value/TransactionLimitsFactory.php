<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\TransactionLimit;
use Routegroup\IngPay\Value\TransactionLimits;

/** @extends Factory<TransactionLimits> */
class TransactionLimitsFactory extends Factory
{
    protected string $payload = TransactionLimits::class;

    protected function definition(): array
    {
        $min = $this->faker()->numberBetween(1, 1000);

        return [
            'minTransaction' => TransactionLimit::factory()->make(['value' => $min])->toArray(),
            'maxTransaction' => TransactionLimit::factory()
                ->make(['value' => $this->faker()->numberBetween($min + 1, 99999999)])->toArray(),
        ];
    }
}
