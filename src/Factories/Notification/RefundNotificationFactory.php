<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Notification;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Notification\RefundNotification;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Types\TransactionType;
use Routegroup\IngPay\Value\Transaction;
use Routegroup\IngPay\Value\TransactionPayment;

/** @extends Factory<RefundNotification> */
class RefundNotificationFactory extends Factory
{
    protected string $payload = RefundNotification::class;

    protected function definition(): array
    {
        $orderId = $this->faker()->unique()->uuid();
        $transactionId = $this->faker()->unique()->uuid();

        return [
            'transaction' => Transaction::factory()->state([
                'orderId' => $orderId,
                'type' => TransactionType::REFUND,
                'status' => TransactionStatus::SETTLED,
            ]),
            'payment' => TransactionPayment::factory()->state([
                'id' => $transactionId,
                'orderId' => $orderId,
                'status' => TransactionStatus::SETTLED,
            ]),
            'originalTransactionId' => $transactionId,
        ];
    }
}
