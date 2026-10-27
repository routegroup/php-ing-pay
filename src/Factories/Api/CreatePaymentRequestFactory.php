<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Api;

use Routegroup\IngPay\Api\CreatePaymentRequest;
use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Value\Customer;

/** @extends Factory<CreatePaymentRequest> */
class CreatePaymentRequestFactory extends Factory
{
    protected string $payload = CreatePaymentRequest::class;

    protected function definition(): array
    {
        return [
            'amount' => $this->faker()->numberBetween(1, 100) * 100,
            'currency' => Currency::PLN,
            'orderId' => $this->faker()->uuid(),
            'customer' => Customer::factory(),
            'returnUrl' => 'https://example.com/return',
            'successReturnUrl' => 'https://example.com/success',
            'failureReturnUrl' => 'https://example.com/failure',
        ];
    }
}
