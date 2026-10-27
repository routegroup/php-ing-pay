<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\PayerCardDataFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static PayerCardDataFactory factory()
 */
class PayerCardData extends Payload
{
    use HasFactory;

    public readonly ?string $brand;

    public readonly ?string $bin;

    public readonly ?string $last4;

    public readonly ?string $type;

    public readonly ?string $countryCodeAlpha2;

    public readonly ?string $countryCodeAlpha3;

    public readonly ?string $countryName;

    public readonly ?string $firstTransactionId;
}
