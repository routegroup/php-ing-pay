<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Paywall;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Paywall\InstallmentsCalculatorRequest;

/** @extends Factory<InstallmentsCalculatorRequest> */
class InstallmentsCalculatorRequestFactory extends Factory
{
    protected string $payload = InstallmentsCalculatorRequest::class;

    protected function definition(): array
    {
        return [
            'amount' => $this->faker()->numberBetween(
                InstallmentsCalculatorRequest::MIN_AMOUNT,
                InstallmentsCalculatorRequest::MAX_AMOUNT,
            ),
        ];
    }
}
