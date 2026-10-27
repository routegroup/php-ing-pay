<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use Routegroup\IngPay\Factories\ResponseFactory;
use Routegroup\IngPay\Response\GetTransactionResponse;
use Routegroup\IngPay\Value\Transaction;

/** @extends ResponseFactory<GetTransactionResponse> */
class GetTransactionResponseFactory extends ResponseFactory
{
    protected string $payload = GetTransactionResponse::class;

    protected function definition(): array
    {
        return [
            'transaction' => Transaction::factory()->make()->toArray(),
        ];
    }
}
