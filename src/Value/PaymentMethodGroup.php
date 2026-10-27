<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Attributes\ListOf;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\PaymentMethodGroupFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static PaymentMethodGroupFactory factory()
 */
class PaymentMethodGroup extends Payload
{
    use HasFactory;

    public readonly ?string $label;

    public readonly ?bool $isOnline;

    public readonly ?int $priority;

    /** @var array<array-key, mixed>|null */
    public readonly ?array $image;

    /** @var list<PaymentMethodChannel> */
    #[ListOf(PaymentMethodChannel::class)]
    public readonly array $channels;
}
