<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Exceptions\InvalidCartException;
use Routegroup\IngPay\Factories\Value\CartFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static CartFactory factory()
 */
class Cart extends Payload
{
    use HasFactory;

    /** @var list<CartItem> */
    public readonly array $items;

    /** @throws InvalidCartException */
    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;
        $items = $attributes['items'] ?? null;

        if (! is_array($items)) {
            throw new InvalidCartException('items', 'is required');
        }

        parent::__construct(['items' => CartItem::fromList($items)]);
    }

    public function toArray(): array
    {
        return ['items' => array_map(
            static fn (CartItem $item): array => $item->toArray(),
            $this->items,
        )];
    }

    /** The paywall carries the cart as one signed field, gzipped at level 5. */
    public function encoded(): string
    {
        return base64_encode((string) gzencode((string) json_encode($this->toArray()), 5));
    }
}
