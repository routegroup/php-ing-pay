<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Response\ApiErrorResponse;

/**
 * Not a ResponseFactory: this payload is built from a body the gateway refused
 * with, and BaseResponse::error() hands it that body already unwrapped.
 *
 * @extends Factory<ApiErrorResponse>
 */
class ApiErrorResponseFactory extends Factory
{
    protected string $payload = ApiErrorResponse::class;

    protected function definition(): array
    {
        return [
            'code' => (string) $this->faker()->numberBetween(400, 599),
            'message' => $this->faker()->sentence(),
            'instance' => $this->faker()->uuid(),
            'errors' => [
                ['property' => $this->faker()->word(), 'message' => $this->faker()->sentence()],
            ],
        ];
    }
}
