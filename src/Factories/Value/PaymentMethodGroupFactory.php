<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\PaymentMethodChannel;
use Routegroup\IngPay\Value\PaymentMethodGroup;

/** @extends Factory<PaymentMethodGroup> */
class PaymentMethodGroupFactory extends Factory
{
    protected string $payload = PaymentMethodGroup::class;

    protected function definition(): array
    {
        return [
            'label' => $this->faker()->randomElement(['Płatność BLIK', 'Przelew tradycyjny', 'Karta']),
            'isOnline' => $this->faker()->boolean(80),
            'priority' => $this->faker()->numberBetween(1, 10),
            'image' => [['png' => 'https://cdn.pay.ing.pl/img/pay/blik.png']],
            'channels' => array_map(
                static fn (): array => PaymentMethodChannel::factory()->make()->toArray(),
                range(1, $this->faker()->numberBetween(1, 3)),
            ),
        ];
    }
}
