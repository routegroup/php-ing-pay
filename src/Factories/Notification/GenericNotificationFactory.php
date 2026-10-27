<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Notification;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Notification\GenericNotification;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Types\TransactionType;
use Routegroup\IngPay\Value\Action;
use Routegroup\IngPay\Value\Transaction;
use Routegroup\IngPay\Value\TransactionPayment;

/** @extends Factory<GenericNotification> */
class GenericNotificationFactory extends Factory
{
    protected string $payload = GenericNotification::class;

    protected function definition(): array
    {
        return [
            'transaction' => Transaction::factory()->withPaymentProfile()->state([
                'type' => TransactionType::SALE,
                'status' => TransactionStatus::SETTLED,
            ]),
            'payment' => TransactionPayment::factory()->state([
                'status' => TransactionStatus::SETTLED,
            ]),
        ];
    }

    public function asPending(): static
    {
        return $this->state([
            'transaction' => Transaction::factory()->state([
                'type' => TransactionType::SALE,
                'status' => TransactionStatus::PENDING,
            ]),
            'payment' => TransactionPayment::factory()->state([
                'status' => TransactionStatus::PENDING,
            ]),
            'action' => Action::factory(),
        ]);
    }
}
