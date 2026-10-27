<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Value\ServiceData;

/** @extends Factory<ServiceData> */
class ServiceDataFactory extends Factory
{
    protected string $payload = ServiceData::class;

    protected function definition(): array
    {
        $code = $this->faker()->randomElement([
            PaymentMethodCode::BLIK,
            PaymentMethodCode::IPKO,
            PaymentMethodCode::MTRANSFER,
            PaymentMethodCode::WIRE_TRANSFER,
        ]);
        $logo = [['png' => $code->getLogo('png')]];

        return [
            'id' => $this->faker()->unique()->uuid(),
            'created' => $this->faker()->unixTime(),
            'isActive' => $this->faker()->boolean(90),
            'paymentMethods' => [
                [
                    'paymentMethod' => match ($code) {
                        PaymentMethodCode::BLIK => 'blik',
                        PaymentMethodCode::WIRE_TRANSFER => 'wt',
                        default => 'pbl',
                    },
                    'paymentMethodCode' => $code->value,
                    'isActive' => true,
                    'isOnline' => $this->faker()->boolean(80),
                    'description' => ucfirst($code->value),
                    'currency' => 'PLN',
                    'transactionLimits' => [
                        'maxTransaction' => ['type' => 'number', 'value' => $this->faker()->numberBetween(10000, 5000000)],
                        'minTransaction' => ['type' => 'number', 'value' => $this->faker()->numberBetween(1, 1000)],
                    ],
                    'paymentMethodImage' => $logo,
                    'paymentMethodCodeImage' => $logo,
                ],
            ],
        ];
    }
}
