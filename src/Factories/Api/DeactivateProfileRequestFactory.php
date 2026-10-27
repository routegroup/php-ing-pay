<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Api;

use Routegroup\IngPay\Api\DeactivateProfileRequest;
use Routegroup\IngPay\Factories\Factory;

/** @extends Factory<DeactivateProfileRequest> */
class DeactivateProfileRequestFactory extends Factory
{
    protected string $payload = DeactivateProfileRequest::class;

    protected function definition(): array
    {
        return [
            'paymentProfileId' => $this->faker()->unique()->uuid(),
        ];
    }
}
