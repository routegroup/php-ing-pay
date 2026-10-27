<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\CartDiscount;

/** @extends Factory<CartDiscount> */
class CartDiscountFactory extends Factory
{
    protected string $payload = CartDiscount::class;

    protected function definition(): array
    {
        $amount = $this->faker()->numberBetween(1, 50) * 100;

        return [
            'amount' => $amount,
            'tax' => (int) round($amount * 0.23),
        ];
    }
}
