<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Attributes\ListOf;
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
    #[ListOf(ServicePaymentMethod::class)]
    public readonly array $paymentMethods;
}
