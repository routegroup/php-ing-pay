<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use Routegroup\IngPay\Factories\ResponseFactory;
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Value\Action;
use Routegroup\IngPay\Value\Transaction;

/** @extends ResponseFactory<TransactionResponse> */
class TransactionResponseFactory extends ResponseFactory
{
    protected string $payload = TransactionResponse::class;

    protected function definition(): array
    {
        return [
            'transaction' => Transaction::factory()->asApiPending(),
            'action' => Action::factory(),
        ];
    }
}
