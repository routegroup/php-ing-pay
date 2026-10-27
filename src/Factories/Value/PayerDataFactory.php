<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\PayerData;

/** @extends Factory<PayerData> */
class PayerDataFactory extends Factory
{
    protected string $payload = PayerData::class;

    protected function definition(): array
    {
        return [
            'name' => $this->faker()->firstName().' '.$this->faker()->lastName(),
            'account' => $this->faker()->numerify('PL'.str_repeat('#', 26)),
        ];
    }
}
