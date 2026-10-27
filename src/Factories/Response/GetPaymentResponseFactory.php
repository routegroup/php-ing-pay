<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use DateTimeImmutable;
use Routegroup\IngPay\Factories\ResponseFactory;
use Routegroup\IngPay\Response\GetPaymentResponse;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Value\Customer;

/**
 * GET /payment/{id} answers with the payment itself, not an envelope — and
 * with three flags and a transaction list that the nested payment value does
 * not carry, so this cannot delegate to the value's factory.
 *
 * @extends ResponseFactory<GetPaymentResponse>
 */
class GetPaymentResponseFactory extends ResponseFactory
{
    protected string $payload = GetPaymentResponse::class;

    protected function definition(): array
    {
        $created = new DateTimeImmutable;

        return [
            'id' => $this->faker()->unique()->uuid(),
            'url' => "https://paywall.sandbox.pay.ing.pl/pay/{$this->faker()->unique()->uuid()}",
            'serviceId' => $this->faker()->uuid(),
            'orderId' => $this->faker()->numerify('ORDER-####-####'),
            'title' => $this->faker()->words(2, true),
            'amount' => $this->faker()->numberBetween(1, 1000) * 100,
            'currency' => Currency::PLN,
            'isActive' => true,
            'created' => $created->getTimestamp(),
            'modified' => $created->getTimestamp() + $this->faker()->numberBetween(0, 600),
            'isGenerated' => $this->faker()->boolean(),
            'isUsed' => $this->faker()->boolean(),
            'isConfirmVisited' => $this->faker()->boolean(),
            'customer' => Customer::factory(),
            'transactions' => [],
        ];
    }
}
