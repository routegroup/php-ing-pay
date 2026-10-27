<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\PayerCardData;

/** @extends Factory<PayerCardData> */
class PayerCardDataFactory extends Factory
{
    protected string $payload = PayerCardData::class;

    protected function definition(): array
    {
        $country = $this->faker()->randomElement([
            ['PL', 'POL', 'Poland'],
            ['DE', 'DEU', 'Germany'],
            ['CZ', 'CZE', 'Czechia'],
        ]);

        return [
            'brand' => $this->faker()->randomElement(['visa', 'mastercard', 'maestro']),
            'bin' => $this->faker()->numerify('######'),
            'last4' => $this->faker()->numerify('####'),
            'type' => $this->faker()->randomElement(['debit', 'credit', 'prepaid']),
            'countryCodeAlpha2' => $country[0],
            'countryCodeAlpha3' => $country[1],
            'countryName' => $country[2],
            'firstTransactionId' => $this->faker()->uuid(),
        ];
    }
}
