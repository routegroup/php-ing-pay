<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use DateTimeImmutable;
use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Value\Customer;
use Routegroup\IngPay\Value\Payment;

/** @extends Factory<Payment> */
class PaymentFactory extends Factory
{
    protected string $payload = Payment::class;

    protected function definition(): array
    {
        $now = new DateTimeImmutable;

        return [
            'id' => $this->faker()->unique()->uuid(),
            'url' => "https://paywall.sandbox.pay.ing.pl/pay/{$this->faker()->uuid()}",
            'serviceId' => $this->faker()->uuid(),
            'orderId' => $this->faker()->unique()->uuid(),
            'title' => $this->faker()->unique()->uuid(),
            'simp' => '',
            'amount' => $this->faker()->numberBetween(1, 1000) * 100,
            'currency' => Currency::PLN,
            'returnUrl' => 'https://example.com/return',
            'successReturnUrl' => 'https://example.com/success',
            'failureReturnUrl' => 'https://example.com/failure',
            'customer' => Customer::factory()->make(),
            'isActive' => true,
            'validTo' => null,
            'created' => $now->getTimestamp(),
            'modified' => $now->getTimestamp(),
        ];
    }
}
