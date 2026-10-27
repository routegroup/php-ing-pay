<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\ApplePayHeader;

/** @extends Factory<ApplePayHeader> */
class ApplePayHeaderFactory extends Factory
{
    protected string $payload = ApplePayHeader::class;

    protected function definition(): array
    {
        return [
            'ephemeralPublicKey' => base64_encode($this->faker()->sha256().$this->faker()->sha256()),
            'publicKeyHash' => base64_encode((string) hex2bin($this->faker()->sha256())),
            // Apple sends a 64-character hex digest, which is also the gateway's minimum.
            'transactionId' => $this->faker()->sha256(),
        ];
    }
}
