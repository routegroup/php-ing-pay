<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\VatExemptionType;
use Routegroup\IngPay\Value\VatExemption;

/** @extends Factory<VatExemption> */
class VatExemptionFactory extends Factory
{
    protected string $payload = VatExemption::class;

    /** Every basis but OTHER, which needs a description and gets its own state. */
    protected function definition(): array
    {
        return [
            'type' => $this->faker()->randomElement(array_filter(
                VatExemptionType::cases(),
                static fn (VatExemptionType $type): bool => $type !== VatExemptionType::OTHER,
            )),
        ];
    }

    public function other(): static
    {
        return $this->state(fn (): array => [
            'type' => VatExemptionType::OTHER,
            'text' => 'Zwolnienie na podstawie '.$this->faker()->numerify('art. ###'),
        ]);
    }
}
