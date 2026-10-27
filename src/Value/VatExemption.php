<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Exceptions\InvalidInvoiceException;
use Routegroup\IngPay\Factories\Value\VatExemptionFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\VatExemptionType;

/**
 * @method static VatExemptionFactory factory()
 */
class VatExemption extends Payload
{
    use HasFactory;

    public readonly VatExemptionType $type;

    public readonly ?string $text;

    /** @throws InvalidInvoiceException */
    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;

        if (($attributes['type'] ?? null) === null) {
            throw new InvalidInvoiceException('basisForVatExemption.type', 'is required');
        }

        parent::__construct($attributes);

        if ($this->type === VatExemptionType::OTHER && ($this->text ?? '') === '') {
            throw new InvalidInvoiceException('basisForVatExemption.text', 'is required when type is OTHER');
        }
    }
}
