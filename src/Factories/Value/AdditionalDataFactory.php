<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\AdditionalData;
use Routegroup\IngPay\Value\Browser;

/** @extends Factory<AdditionalData> */
class AdditionalDataFactory extends Factory
{
    protected string $payload = AdditionalData::class;

    protected function definition(): array
    {
        return [
            'browser' => Browser::factory()->make()->toArray(),
        ];
    }
}
