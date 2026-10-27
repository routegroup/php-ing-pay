<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\PaymentMethodChannelFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;

/**
 * @method static PaymentMethodChannelFactory factory()
 */
class PaymentMethodChannel extends Payload
{
    use HasFactory;

    public readonly ?PaymentMethod $paymentMethod;

    public readonly ?PaymentMethodCode $paymentMethodCode;

    public readonly ?string $label;

    public readonly ?int $priority;

    public readonly ?bool $isOnline;

    /** @var array<array-key, mixed>|null */
    public readonly ?array $image;
}
