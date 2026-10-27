<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Exceptions\InvalidInvoiceException;
use Routegroup\IngPay\Factories\Value\InvoiceBuyerFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\InvoiceBuyerType;
use Routegroup\IngPay\Types\InvoiceIdType;

/**
 * @method static InvoiceBuyerFactory factory()
 */
class InvoiceBuyer extends Payload
{
    use HasFactory;

    public readonly InvoiceBuyerType $type;

    public readonly string $email;

    public readonly string $fullName;

    public readonly string $street;

    public readonly string $city;

    public readonly string $postalCode;

    public readonly string $countryCodeAlpha2;

    public readonly ?string $idCountryCodeAlpha2;

    public readonly ?InvoiceIdType $idType;

    public readonly ?string $idNumber;

    /** @throws InvalidInvoiceException */
    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;

        foreach (['type', 'email', 'fullName', 'street', 'city', 'postalCode', 'countryCodeAlpha2'] as $field) {
            if (($attributes[$field] ?? null) === null || $attributes[$field] === '') {
                throw new InvalidInvoiceException("buyer.$field", 'is required');
            }
        }

        parent::__construct($attributes);

        if ($this->type === InvoiceBuyerType::COMPANY && (! $this->idType instanceof InvoiceIdType || $this->idNumber === null)) {
            throw new InvalidInvoiceException('buyer.idType', 'idType and idNumber are required for a COMPANY');
        }

        if ($this->idType === InvoiceIdType::VAT_ID && $this->idCountryCodeAlpha2 === null) {
            throw new InvalidInvoiceException('buyer.idCountryCodeAlpha2', 'is required when idType is VAT_ID');
        }
    }
}
