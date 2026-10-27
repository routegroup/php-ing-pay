<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Attributes\ListOf;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Exceptions\InvalidInvoiceException;
use Routegroup\IngPay\Factories\Value\InvoiceFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static InvoiceFactory factory()
 */
class Invoice extends Payload
{
    use HasFactory;

    public readonly ?string $invoiceId;

    public readonly ?VatExemption $basisForVatExemption;

    public readonly ?bool $issueInvoice;

    public readonly InvoiceBuyer $buyer;

    /** @var list<InvoicePosition> */
    #[ListOf(InvoicePosition::class)]
    public readonly array $positions;

    /** @throws InvalidInvoiceException */
    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;

        if (! isset($attributes['buyer'])) {
            throw new InvalidInvoiceException('buyer', 'is required');
        }

        $positions = $attributes['positions'] ?? null;

        if (! is_array($positions)) {
            throw new InvalidInvoiceException('positions', 'is required');
        }

        $attributes['positions'] = InvoicePosition::fromList(
            $positions,
            ($attributes['invoiceId'] ?? null) !== null,
        );

        parent::__construct($attributes);
    }

    /** The paywall carries the invoice as one signed field, gzipped at level 5. */
    public function encoded(): string
    {
        return base64_encode((string) gzencode((string) json_encode($this->toArray()), 5));
    }
}
