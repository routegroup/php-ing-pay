<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\Billing;

/** @extends Factory<Billing> */
class BillingFactory extends Factory
{
    protected string $payload = Billing::class;

    protected function definition(): array
    {
        return [
            'firstName' => $this->faker()->firstName(),
            'lastName' => $this->faker()->lastName(),
            'company' => $this->faker()->company(),
            'street' => $this->faker()->streetName(),
            'city' => $this->faker()->city(),
            'region' => $this->faker()->word(),
            'postalCode' => $this->faker()->postcode(),
            'countryCodeAlpha2' => $this->faker()->countryCode(),
            'taxId' => $this->faker()->numerify('##########'),
        ];
    }
}
