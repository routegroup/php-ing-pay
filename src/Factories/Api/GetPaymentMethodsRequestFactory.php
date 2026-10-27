<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Api;

use Routegroup\IngPay\Api\GetPaymentMethodsRequest;
use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\Currency;

/** @extends Factory<GetPaymentMethodsRequest> */
class GetPaymentMethodsRequestFactory extends Factory
{
    protected string $payload = GetPaymentMethodsRequest::class;

    protected function definition(): array
    {
        return [
            'amount' => $this->faker()->numberBetween(1, 100) * 100,
            'currency' => Currency::PLN,
        ];
    }
}
