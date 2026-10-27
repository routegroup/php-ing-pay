<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use Routegroup\IngPay\Factories\ResponseFactory;
use Routegroup\IngPay\Response\PaymentResponse;
use Routegroup\IngPay\Value\Payment;

/** @extends ResponseFactory<PaymentResponse> */
class PaymentResponseFactory extends ResponseFactory
{
    protected string $payload = PaymentResponse::class;

    protected function definition(): array
    {
        return [
            'payment' => Payment::factory(),
        ];
    }
}
