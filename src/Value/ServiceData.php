<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\ServiceDataFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static ServiceDataFactory factory()
 */
class ServiceData extends Payload
{
    use HasFactory;

    public readonly string $id;

    public readonly int $created;

    public readonly bool $isActive;

    /** @var list<ServicePaymentMethod> */
    public readonly array $paymentMethods;

    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;
        $methods = $attributes['paymentMethods'] ?? [];

        $attributes['paymentMethods'] = array_map(
            static fn (mixed $method): mixed => is_array($method)
                ? new ServicePaymentMethod($method)
                : $method,
            is_array($methods) ? $methods : [],
        );

        parent::__construct($attributes);
    }

    public function toArray(): array
    {
        $attributes = parent::toArray();

        $attributes['paymentMethods'] = array_map(
            static fn (mixed $method): mixed => $method instanceof ServicePaymentMethod
                ? $method->toArray()
                : $method,
            is_array($attributes['paymentMethods'] ?? null) ? $attributes['paymentMethods'] : [],
        );

        return $attributes;
    }
}
