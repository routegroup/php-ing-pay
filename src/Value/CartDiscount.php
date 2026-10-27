<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Exceptions\InvalidCartException;
use Routegroup\IngPay\Factories\Value\CartDiscountFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static CartDiscountFactory factory()
 */
class CartDiscount extends Payload
{
    use HasFactory;

    public readonly int $amount;

    public readonly int $tax;

    /** @throws InvalidCartException */
    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;

        // The gateway takes the pair or nothing; either half alone is refused.
        foreach (['amount', 'tax'] as $field) {
            if (($attributes[$field] ?? null) === null) {
                throw new InvalidCartException("items.discount.$field", 'is required once a discount is given');
            }
        }

        parent::__construct($attributes);
    }
}
