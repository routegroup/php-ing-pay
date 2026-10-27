<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\Cart;
use Routegroup\IngPay\Value\CartItem;

/** @extends Factory<Cart> */
class CartFactory extends Factory
{
    protected string $payload = Cart::class;

    protected function definition(): array
    {
        return [
            'items' => array_map(
                static fn (): array => CartItem::factory()->make()->toArray(),
                range(1, $this->faker()->numberBetween(1, 4)),
            ),
        ];
    }
}
