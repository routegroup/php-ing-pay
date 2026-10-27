<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use Routegroup\IngPay\Factories\ResponseFactory;
use Routegroup\IngPay\Response\CanRefundResponse;

/** @extends ResponseFactory<CanRefundResponse> */
class CanRefundResponseFactory extends ResponseFactory
{
    protected string $payload = CanRefundResponse::class;

    protected function definition(): array
    {
        $amount = $this->faker()->numberBetween(1, 10000) * 100;

        return [
            'id' => $this->faker()->unique()->uuid(),
            'refundable' => true,
            'balance' => true,
            'fullRefund' => $amount,
            'partialRefund' => [
                'maxRefundAmount' => $amount,
                'minRefundAmount' => 1,
            ],
        ];
    }
}
