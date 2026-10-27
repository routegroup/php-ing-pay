<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Paywall;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;
use Routegroup\IngPay\Types\Currency;

/** @extends Factory<CreateTransactionRequest> */
class CreateTransactionRequestFactory extends Factory
{
    protected string $payload = CreateTransactionRequest::class;

    protected function definition(): array
    {
        return [
            'amount' => $this->faker()->numberBetween(1, 100000) * 100,
            'currency' => Currency::PLN,
            'orderId' => $this->faker()->unique()->uuid(),
            'customerFirstName' => $this->faker()->firstName(),
            'customerLastName' => $this->faker()->lastName(),
        ];
    }

    public function withOptional(): static
    {
        return $this->state([
            'customerEmail' => $this->faker()->email(),
            'customerPhone' => $this->faker()->numerify('#########'),
            'urlSuccess' => 'https://example.com/success',
            'urlFailure' => 'https://example.com/failure',
            'urlReturn' => 'https://example.com/return',
            'orderDescription' => $this->faker()->sentence(),
        ]);
    }
}
