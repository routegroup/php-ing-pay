<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\ApiError;

/** @extends Factory<ApiError> */
class ApiErrorFactory extends Factory
{
    protected string $payload = ApiError::class;

    protected function definition(): array
    {
        $field = $this->faker()->word();

        return [
            'property' => 'instance.'.$field,
            'message' => $this->faker()->randomElement([
                'requires property "'.$field.'"',
                'is not of a type(s) string',
                'does not meet minimum length of 1',
            ]),
        ];
    }
}
