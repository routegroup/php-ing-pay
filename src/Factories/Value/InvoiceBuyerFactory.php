<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\InvoiceBuyerType;
use Routegroup\IngPay\Types\InvoiceIdType;
use Routegroup\IngPay\Value\InvoiceBuyer;

/** @extends Factory<InvoiceBuyer> */
class InvoiceBuyerFactory extends Factory
{
    protected string $payload = InvoiceBuyer::class;

    protected function definition(): array
    {
        return [
            'type' => InvoiceBuyerType::PERSON,
            'email' => $this->faker()->email(),
            'fullName' => $this->faker()->name(),
            'street' => $this->faker()->streetAddress(),
            'city' => $this->faker()->city(),
            'postalCode' => $this->faker()->numerify('##-###'),
            'countryCodeAlpha2' => 'PL',
        ];
    }

    public function company(): static
    {
        return $this->state(fn (): array => [
            'type' => InvoiceBuyerType::COMPANY,
            'idType' => InvoiceIdType::VAT_ID,
            'idNumber' => $this->faker()->numerify('##########'),
            'idCountryCodeAlpha2' => 'PL',
        ]);
    }
}
