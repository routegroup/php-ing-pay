<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\Multipayout;

/** @extends Factory<Multipayout> */
class MultipayoutFactory extends Factory
{
    protected string $payload = Multipayout::class;

    protected function definition(): array
    {
        return [
            'ban' => $this->faker()->numerify(str_repeat('#', 26)),
            'amount' => $this->faker()->numberBetween(1, 1000) * 100,
            'label' => 'Nazwa firmy '.$this->faker()->numberBetween(0, 99),
        ];
    }

    public function withTitle(): static
    {
        return $this->state(fn (): array => [
            'title' => 'Platnosc '.$this->faker()->numerify('######'),
        ]);
    }
}
