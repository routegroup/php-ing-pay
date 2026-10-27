<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use DateTimeImmutable;
use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\TransactionPayment;

/** @extends Factory<TransactionPayment> */
class TransactionPaymentFactory extends Factory
{
    protected string $payload = TransactionPayment::class;

    protected function definition(): array
    {
        $now = new DateTimeImmutable;

        return [
            'id' => $this->faker()->unique()->uuid(),
            'amount' => $this->faker()->numberBetween(1, 1000) * 100,
            'status' => TransactionStatus::SETTLED,
            'created' => $now->getTimestamp(),
            'orderId' => $this->faker()->unique()->uuid(),
            'currency' => Currency::PLN,
            'modified' => $now->getTimestamp(),
            'serviceId' => $this->faker()->uuid(),
        ];
    }
}
