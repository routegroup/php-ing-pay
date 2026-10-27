<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use Routegroup\IngPay\Factories\ResponseFactory;
use Routegroup\IngPay\Response\RefundResponse;
use Routegroup\IngPay\Value\Transaction;

/** @extends ResponseFactory<RefundResponse> */
class RefundResponseFactory extends ResponseFactory
{
    protected string $payload = RefundResponse::class;

    protected function definition(): array
    {
        return [
            'transaction' => Transaction::factory()->asApiRefund(),
        ];
    }
}
