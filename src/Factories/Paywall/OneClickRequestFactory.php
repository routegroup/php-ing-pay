<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Paywall;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Paywall\OneClickRequest;
use Routegroup\IngPay\Types\Currency;

/** @extends Factory<OneClickRequest> */
class OneClickRequestFactory extends Factory
{
    protected string $payload = OneClickRequest::class;

    protected function definition(): array
    {
        return [
            'amount' => $this->faker()->numberBetween(1, 100000) * 100,
            'currency' => Currency::PLN,
            'orderId' => $this->faker()->unique()->uuid(),
            'customerId' => $this->faker()->numerify(),
            'customerFirstName' => $this->faker()->firstName(),
            'customerLastName' => $this->faker()->lastName(),
            'customerEmail' => $this->faker()->email(),
        ];
    }

    public function withOptional(): static
    {
        return $this->state([
            'customerPhone' => $this->faker()->numerify('#########'),
            'orderDescription' => $this->faker()->sentence(),
            'urlSuccess' => 'https://example.com/success',
            'urlFailure' => 'https://example.com/failure',
            'urlReturn' => 'https://example.com/return',
            'urlCancel' => 'https://example.com/cancel',
        ]);
    }
}
