<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Api;

use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Value\Billing;
use Routegroup\IngPay\Value\Card;
use Routegroup\IngPay\Value\Customer;
use Routegroup\IngPay\Value\Shipping;

/** @extends Factory<CreateTransactionRequest> */
class CreateTransactionRequestFactory extends Factory
{
    protected string $payload = CreateTransactionRequest::class;

    protected function definition(): array
    {
        return [
            'amount' => $this->faker()->numberBetween(1, 100000) * 100,
            'currency' => Currency::PLN,
            'orderId' => $this->faker()->unique()->uuid(),
            'paymentMethod' => PaymentMethod::PAY_BY_LINK,
            'paymentMethodCode' => PaymentMethodCode::IPKO,
            'successReturnUrl' => 'https://example.com/success',
            'failureReturnUrl' => 'https://example.com/failure',
            'customer' => Customer::factory(),
            'billing' => Billing::factory(),
            'shipping' => Shipping::factory(),
        ];
    }

    public function asCard(): static
    {
        return $this->state([
            'paymentMethod' => PaymentMethod::CARD,
            'paymentMethodCode' => PaymentMethodCode::ONECLICK,
            'card' => Card::factory(),
            'additionalData' => [
                'browser' => [
                    'ip' => '127.0.0.1',
                    'language' => 'pl-PL',
                    'jsEnabled' => true,
                    'timezoneOffset' => 100,
                    'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66 Safari/537.36',
                    'accept' => 'application/json, text/javascript, */*; q=0.01',
                    'javaEnabled' => false,
                    'screenColorDepth' => 24,
                    'screenHeight' => 1080,
                    'screenWidth' => 2560,
                ],
            ],
        ]);
    }
}
