<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Value\ServicePaymentMethod;
use Routegroup\IngPay\Value\TransactionLimits;

/** @extends Factory<ServicePaymentMethod> */
class ServicePaymentMethodFactory extends Factory
{
    protected string $payload = ServicePaymentMethod::class;

    protected function definition(): array
    {
        $code = $this->faker()->randomElement([
            PaymentMethodCode::BLIK, PaymentMethodCode::IPKO,
            PaymentMethodCode::MTRANSFER, PaymentMethodCode::WIRE_TRANSFER,
        ]);

        return [
            'paymentMethod' => $code === PaymentMethodCode::BLIK ? 'blik' : 'pbl',
            'paymentMethodCode' => $code->value,
            'isActive' => $this->faker()->boolean(90),
            'isOnline' => $this->faker()->boolean(80),
            'description' => ucfirst($code->value),
            'currency' => 'PLN',
            'transactionLimits' => TransactionLimits::factory()->make()->toArray(),
            'paymentMethodImage' => [['png' => $code->getLogo('png')]],
            'paymentMethodCodeImage' => [['png' => $code->getLogo('png')]],
        ];
    }
}
