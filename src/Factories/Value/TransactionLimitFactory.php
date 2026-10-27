<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\TransactionLimit;

/** @extends Factory<TransactionLimit> */
class TransactionLimitFactory extends Factory
{
    protected string $payload = TransactionLimit::class;

    protected function definition(): array
    {
        return ['type' => 'number', 'value' => $this->faker()->numberBetween(1, 99999999)];
    }
}
