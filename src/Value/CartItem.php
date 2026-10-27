<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Exceptions\InvalidCartException;
use Routegroup\IngPay\Factories\Value\CartItemFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\CartItemState;
use Routegroup\IngPay\Types\LeaseTaxStake;

/**
 * @method static CartItemFactory factory()
 */
class CartItem extends Payload
{
    use HasFactory;

    public readonly string $id;

    public readonly string $name;

    public readonly int $amount;

    public readonly int $tax;

    public readonly LeaseTaxStake $taxStake;

    public readonly int $quantity;

    public readonly string $url;

    public readonly string $categoryId;

    public readonly ?string $unit;

    public readonly ?CartItemState $state;

    public readonly ?CartDiscount $discount;

    /**
     * @param  list<array<string, mixed>|CartItem>  $items
     * @return list<CartItem>
     *
     * @throws InvalidCartException
     */
    public static function fromList(array $items): array
    {
        if ($items === []) {
            throw new InvalidCartException('items', 'must hold at least one item');
        }

        return array_map(
            static fn (mixed $i): self => $i instanceof self ? $i : new self((array) $i),
            // @phpstan-ignore arrayValues.list (decoded JSON can carry gaps in its keys)
            array_values($items),
        );
    }

    /** @throws InvalidCartException */
    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;

        if (isset($attributes['taxStake']) && ! $attributes['taxStake'] instanceof LeaseTaxStake) {
            $stake = LeaseTaxStake::fromRate($attributes['taxStake']);

            if (! $stake instanceof LeaseTaxStake) {
                throw new InvalidCartException('items.taxStake', 'is not a rate Lease Now accepts');
            }

            $attributes['taxStake'] = $stake;
        }

        foreach (['id', 'name', 'amount', 'tax', 'taxStake', 'quantity', 'url', 'categoryId'] as $field) {
            if (($attributes[$field] ?? null) === null || $attributes[$field] === '') {
                throw new InvalidCartException("items.$field", 'is required');
            }
        }

        if (! is_numeric($attributes['quantity']) || $attributes['quantity'] < 1) {
            throw new InvalidCartException('items.quantity', 'must be at least 1');
        }

        if (filter_var($attributes['url'], FILTER_VALIDATE_URL) === false) {
            throw new InvalidCartException('items.url', 'must be a valid URL');
        }

        parent::__construct($attributes);
    }

    public function toArray(): array
    {
        $attributes = parent::toArray();

        // transformValues renders a backed enum as its value, which would quote
        // the percentage the gateway insists on receiving as a number.
        $attributes['taxStake'] = $this->taxStake->encoded();

        return $attributes;
    }
}
