<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

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
    public readonly array $channels;

    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;
        $channels = $attributes['channels'] ?? [];

        $attributes['channels'] = array_map(
            static fn (mixed $channel): mixed => is_array($channel)
                ? new PaymentMethodChannel($channel)
                : $channel,
            is_array($channels) ? $channels : [],
        );

        parent::__construct($attributes);
    }

    public function toArray(): array
    {
        $attributes = parent::toArray();

        $attributes['channels'] = array_map(
            static fn (mixed $channel): mixed => $channel instanceof PaymentMethodChannel
                ? $channel->toArray()
                : $channel,
            is_array($attributes['channels'] ?? null) ? $attributes['channels'] : [],
        );

        return $attributes;
    }
}
