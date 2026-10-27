<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use Routegroup\IngPay\Factories\ResponseFactory;
use Routegroup\IngPay\Response\TrustedIpsResponse;

/** @extends ResponseFactory<TrustedIpsResponse> */
class TrustedIpsResponseFactory extends ResponseFactory
{
    protected string $payload = TrustedIpsResponse::class;

    protected function definition(): array
    {
        return [
            'trustedIps' => array_map(
                fn (): string => $this->faker()->unique()->ipv4(),
                range(1, $this->faker()->numberBetween(1, 5)),
            ),
        ];
    }

    /** The shape PUT answers with, which carries no list at all. */
    public function afterPut(): static
    {
        return $this->state(fn (): array => ['trustedIps' => null, 'status' => 'success']);
    }
}
