<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\Invoice;
use Routegroup\IngPay\Value\InvoiceBuyer;
use Routegroup\IngPay\Value\InvoicePosition;

/** @extends Factory<Invoice> */
class InvoiceFactory extends Factory
{
    protected string $payload = Invoice::class;

    /** invoiceId is set, so this is the Split Payment variant and positions carry taxAmount. */
    protected function definition(): array
    {
        return [
            'invoiceId' => $this->faker()->numerify('##/##/##'),
            'buyer' => InvoiceBuyer::factory()->make()->toArray(),
            'positions' => array_map(
                static fn (): array => InvoicePosition::factory()->make()->toArray(),
                range(1, $this->faker()->numberBetween(1, 3)),
            ),
        ];
    }
}
