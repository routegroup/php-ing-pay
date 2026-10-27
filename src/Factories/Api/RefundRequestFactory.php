<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Api;

use Routegroup\IngPay\Api\RefundRequest;
use Routegroup\IngPay\Factories\Factory;

/** @extends Factory<RefundRequest> */
class RefundRequestFactory extends Factory
{
    protected string $payload = RefundRequest::class;

    protected function definition(): array
    {
        return [
            'amount' => $this->faker()->numberBetween(1, 100) * 100,
        ];
    }
}
