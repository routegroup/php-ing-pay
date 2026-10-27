<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Payload;

class PayerCardData extends Payload
{
    public readonly ?string $brand;

    public readonly ?string $bin;

    public readonly ?string $last4;

    public readonly ?string $type;

    public readonly ?string $countryCodeAlpha2;

    public readonly ?string $countryCodeAlpha3;

    public readonly ?string $countryName;

    public readonly ?string $firstTransactionId;
}
