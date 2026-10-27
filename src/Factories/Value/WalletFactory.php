<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\ApplePayPaymentData;
use Routegroup\IngPay\Value\Wallet;

/** @extends Factory<Wallet> */
class WalletFactory extends Factory
{
    protected string $payload = Wallet::class;

    /** Google Pay by default; applePay() swaps in the other provider's shape. */
    protected function definition(): array
    {
        return [
            'signature' => base64_encode($this->faker()->text(200)),
            'protocolVersion' => $this->faker()->randomElement(['ECv1', 'ECv2']),
            'signedMessage' => json_encode([
                'encryptedMessage' => base64_encode($this->faker()->text(300)),
                'ephemeralPublicKey' => base64_encode($this->faker()->sha256()),
                'tag' => base64_encode((string) hex2bin($this->faker()->sha256())),
            ]),
        ];
    }

    public function applePay(): self
    {
        return $this->state(fn (): array => [
            'signature' => null,
            'protocolVersion' => null,
            'signedMessage' => null,
            'paymentData' => ApplePayPaymentData::factory()->make()->toArray(),
        ]);
    }
}
