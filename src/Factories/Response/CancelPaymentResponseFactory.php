<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use DateTimeImmutable;
use Routegroup\IngPay\Factories\ResponseFactory;
use Routegroup\IngPay\Response\CancelPaymentResponse;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Customer;

/** @extends ResponseFactory<CancelPaymentResponse> */
class CancelPaymentResponseFactory extends ResponseFactory
{
    protected string $payload = CancelPaymentResponse::class;

    protected function definition(): array
    {
        $now = new DateTimeImmutable;

        return [
            'id' => $this->faker()->unique()->uuid(),
            'url' => "https://paywall.sandbox.pay.ing.pl/pay/{$this->faker()->unique()->uuid()}",
            'serviceId' => $this->faker()->uuid(),
            'orderId' => $this->faker()->unique()->uuid(),
            'title' => '',
            'simp' => '',
            'amount' => $this->faker()->numberBetween(1, 1000) * 100,
            'currency' => Currency::PLN,
            'status' => TransactionStatus::CANCELLED,
            'isActive' => true,
            'validTo' => null,
            'created' => $now->getTimestamp(),
            'modified' => $now->getTimestamp(),
            'isGenerated' => false,
            'isUsed' => false,
            'usedAt' => null,
            'isConfirmVisited' => false,
            'confirmVisitedAt' => null,
            'returnUrl' => 'https://example.com/return',
            'failureReturnUrl' => 'https://example.com/failure',
            'successReturnUrl' => 'https://example.com/success',
            'customer' => Customer::factory(),
            'transactions' => [],
        ];
    }
}
