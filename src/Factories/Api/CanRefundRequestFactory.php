<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Api;

use Routegroup\IngPay\Api\CanRefundRequest;
use Routegroup\IngPay\Factories\Factory;

/** @extends Factory<CanRefundRequest> */
class CanRefundRequestFactory extends Factory
{
    protected string $payload = CanRefundRequest::class;

    protected function definition(): array
    {
        return [
            'amount' => $this->faker()->numberBetween(1, 100) * 100,
        ];
    }
}
