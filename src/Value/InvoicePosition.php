<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Exceptions\InvalidInvoiceException;
use Routegroup\IngPay\Factories\Value\InvoicePositionFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\TaxStake;

/**
 * @method static InvoicePositionFactory factory()
 */
class InvoicePosition extends Payload
{
    use HasFactory;

    public readonly string $name;

    public readonly string $code;

    public readonly int $quantity;

    public readonly string $unit;

    public readonly int $grossAmount;

    public readonly TaxStake $taxStake;

    public readonly ?int $taxAmount;

    public readonly ?int $discountAmount;

    /**
     * @param  list<array<string, mixed>|InvoicePosition>  $positions
     * @return list<InvoicePosition>
     *
     * @throws InvalidInvoiceException
     */
    public static function fromList(array $positions, bool $requireTaxAmount = false): array
    {
        if ($positions === []) {
            throw new InvalidInvoiceException('positions', 'must hold at least one position');
        }

        return array_map(
            static fn (mixed $p): self => $p instanceof self ? $p : new self((array) $p, $requireTaxAmount),
            // @phpstan-ignore arrayValues.list (decoded JSON can carry gaps in its keys)
            array_values($positions),
        );
    }

    /** @throws InvalidInvoiceException */
    public function __construct($attributes = [], bool $requireTaxAmount = false)
    {
        $attributes = (array) $attributes;

        if (isset($attributes['taxStake']) && ! $attributes['taxStake'] instanceof TaxStake) {
            $stake = TaxStake::fromPercentage($attributes['taxStake']);

            if (! $stake instanceof TaxStake) {
                throw new InvalidInvoiceException('positions.taxStake', 'is not a rate the gateway accepts');
            }

            $attributes['taxStake'] = $stake;
        }

        $required = ['name', 'code', 'unit', 'taxStake', 'grossAmount'];

        if ($requireTaxAmount) {
            $required[] = 'taxAmount';
        }

        foreach ($required as $field) {
            if (($attributes[$field] ?? null) === null || $attributes[$field] === '') {
                throw new InvalidInvoiceException("positions.$field", 'is required');
            }
        }

        $quantity = $attributes['quantity'] ?? null;

        if (! is_numeric($quantity) || $quantity < 0) {
            throw new InvalidInvoiceException('positions.quantity', 'is required and cannot be negative');
        }

        parent::__construct($attributes);
    }
}
