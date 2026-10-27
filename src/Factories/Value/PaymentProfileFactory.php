<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\PaymentProfile;

/** @extends Factory<PaymentProfile> */
class PaymentProfileFactory extends Factory
{
    protected string $payload = PaymentProfile::class;

    protected function definition(): array
    {
        return [
            'id' => $this->faker()->uuid(),
            'merchantMid' => $this->faker()->numerify('mid-#####'),
            'merchantCustomerId' => $this->faker()->uuid(),
            'firstName' => $this->faker()->firstName(),
            'lastName' => $this->faker()->lastName(),
            'email' => $this->faker()->email(),
            'maskedNumber' => $this->faker()->numerify('4####*****####'),
            'month' => $this->faker()->numberBetween(1, 12),
            'year' => $this->faker()->numberBetween(2027, 2035),
            'organization' => $this->faker()->company(),
            'isActive' => $this->faker()->boolean(),
            'profile' => $this->faker()->randomElement(['oneclick', 'recurring']),
            'firstTransactionId' => $this->faker()->uuid(),
        ];
    }
}
