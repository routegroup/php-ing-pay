<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Api;

use Routegroup\IngPay\Api\ChargeProfileRequest;
use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\Currency;

/** @extends Factory<ChargeProfileRequest> */
class ChargeProfileRequestFactory extends Factory
{
    protected string $payload = ChargeProfileRequest::class;

    protected function definition(): array
    {
        return [
            'amount' => $this->faker()->numberBetween(1, 100) * 100,
            'currency' => $this->faker()->randomElement(Currency::cases()),
        ];
    }
}
