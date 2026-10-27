<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\ApplePayHeader;
use Routegroup\IngPay\Value\ApplePayPaymentData;

/** @extends Factory<ApplePayPaymentData> */
class ApplePayPaymentDataFactory extends Factory
{
    protected string $payload = ApplePayPaymentData::class;

    protected function definition(): array
    {
        return [
            'version' => $this->faker()->randomElement(['EC_v1', 'RSA_v1']),
            'data' => base64_encode($this->faker()->text(400)),
            'signature' => base64_encode($this->faker()->text(600)),
            'header' => ApplePayHeader::factory()->make()->toArray(),
        ];
    }
}
