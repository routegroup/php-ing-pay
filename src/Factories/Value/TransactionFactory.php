<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Types\TransactionSource;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Types\TransactionType;
use Routegroup\IngPay\Value\Transaction;

/** @extends Factory<Transaction> */
class TransactionFactory extends Factory
{
    protected string $payload = Transaction::class;

    protected function definition(): array
    {
        $created = $this->faker()->unixTime();

        return [
            'id' => $this->faker()->uuid(),
            'type' => $this->faker()->randomElement(TransactionType::cases()),
            'status' => $this->faker()->randomElement(TransactionStatus::cases()),
            'source' => $this->faker()->randomElement(TransactionSource::cases()),
            'created' => $created,
            'modified' => $created + $this->faker()->numberBetween(0, 3600),
            'serviceId' => $this->faker()->uuid(),
            'amount' => $this->faker()->numberBetween(100, 500000),
            'currency' => $this->faker()->randomElement(Currency::cases()),
            'orderId' => $this->faker()->numerify('ORDER-####-####'),
        ];
    }

    public function settledCard(): static
    {
        return $this->state([
            'status' => TransactionStatus::SETTLED,
            'type' => TransactionType::SALE,
            'paymentMethod' => PaymentMethod::CARD,
            'paymentMethodCode' => $this->faker()->randomElement([
                PaymentMethodCode::BLIK,
                PaymentMethodCode::ING,
            ]),
            'payerCardData' => PayerCardDataFactory::new()->make(),
            'fee' => FeeFactory::new()->make(),
        ]);
    }
}
