<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\Installment;

/** @extends Factory<Installment> */
class InstallmentFactory extends Factory
{
    protected string $payload = Installment::class;

    protected function definition(): array
    {
        return [
            'period' => $this->faker()->numberBetween(3, 10),
        ];
    }
}
