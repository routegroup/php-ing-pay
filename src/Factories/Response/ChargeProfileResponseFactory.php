<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use Routegroup\IngPay\Factories\ResponseFactory;
use Routegroup\IngPay\Response\ChargeProfileResponse;
use Routegroup\IngPay\Value\Transaction;

/** @extends ResponseFactory<ChargeProfileResponse> */
class ChargeProfileResponseFactory extends ResponseFactory
{
    protected string $payload = ChargeProfileResponse::class;

    protected function definition(): array
    {
        return [
            'transaction' => Transaction::factory()->withPaymentProfile(),
        ];
    }
}
