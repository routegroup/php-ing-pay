<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Api;

use Routegroup\IngPay\Api\CancelPaymentRequest;
use Routegroup\IngPay\Factories\Factory;

/** @extends Factory<CancelPaymentRequest> */
class CancelPaymentRequestFactory extends Factory
{
    protected string $payload = CancelPaymentRequest::class;

    protected function definition(): array
    {
        return [
            'paymentId' => $this->faker()->unique()->uuid(),
        ];
    }
}
