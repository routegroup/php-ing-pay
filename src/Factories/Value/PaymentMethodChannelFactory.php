<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Value\PaymentMethodChannel;

/** @extends Factory<PaymentMethodChannel> */
class PaymentMethodChannelFactory extends Factory
{
    protected string $payload = PaymentMethodChannel::class;

    protected function definition(): array
    {
        $code = $this->faker()->randomElement([
            PaymentMethodCode::BLIK, PaymentMethodCode::IPKO, PaymentMethodCode::MTRANSFER,
        ]);

        return [
            'paymentMethod' => $code === PaymentMethodCode::BLIK ? 'blik' : 'pbl',
            'paymentMethodCode' => $code->value,
            'label' => ucfirst($code->value),
            'priority' => $this->faker()->numberBetween(1, 20),
            'isOnline' => $this->faker()->boolean(80),
            'image' => [['png' => $code->getLogo('png')]],
        ];
    }
}
