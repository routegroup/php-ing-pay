<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\PaymentSummary;

/** @extends Factory<PaymentSummary> */
class PaymentSummaryFactory extends Factory
{
    protected string $payload = PaymentSummary::class;

    protected function definition(): array
    {
        return [
            'id' => $this->faker()->uuid(),
            'status' => $this->faker()->randomElement(TransactionStatus::cases()),
        ];
    }
}
