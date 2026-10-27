<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\CartItemState;
use Routegroup\IngPay\Types\LeaseTaxStake;
use Routegroup\IngPay\Value\CartDiscount;
use Routegroup\IngPay\Value\CartItem;

/** @extends Factory<CartItem> */
class CartItemFactory extends Factory
{
    protected string $payload = CartItem::class;

    protected function definition(): array
    {
        $stake = $this->faker()->randomElement(LeaseTaxStake::cases());
        // Net, in grosze, around the 1000 PLN the leasing floor asks for.
        $amount = $this->faker()->numberBetween(1000, 20000) * 100;

        return [
            'id' => (string) $this->faker()->unique()->numberBetween(1, 99999),
            'categoryId' => $this->faker()->slug(2),
            'name' => $this->faker()->words(3, true),
            'amount' => $amount,
            'tax' => (int) round($amount * $this->rate($stake) / 100),
            'taxStake' => $stake,
            'quantity' => $this->faker()->numberBetween(1, 10),
            'unit' => $this->faker()->randomElement(['szt', 'kpl', 'kg', 'm']),
            'url' => $this->faker()->url(),
            'state' => $this->faker()->randomElement(CartItemState::cases()),
            'discount' => CartDiscount::factory()->make()->toArray(),
        ];
    }

    /** A non-numeric basis carries no percentage, so it carries no tax either. */
    private function rate(LeaseTaxStake $stake): int
    {
        return is_numeric($stake->value) ? (int) $stake->value : 0;
    }
}
