<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Api;

use Routegroup\IngPay\Api\PutTrustedIpsRequest;
use Routegroup\IngPay\Factories\Factory;

/** @extends Factory<PutTrustedIpsRequest> */
class PutTrustedIpsRequestFactory extends Factory
{
    protected string $payload = PutTrustedIpsRequest::class;

    protected function definition(): array
    {
        return [
            'trustedIps' => $this->faker()->randomElements(
                array_map(fn (): string => $this->faker()->unique()->ipv4(), range(1, 6)),
                $this->faker()->numberBetween(1, 4),
            ),
        ];
    }
}
