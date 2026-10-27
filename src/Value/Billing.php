<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\BillingFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static BillingFactory factory()
 */
class Billing extends Payload
{
    use HasFactory;

    public readonly string $firstName;

    public readonly string $lastName;

    public readonly ?string $company;

    public readonly ?string $street;

    public readonly ?string $city;

    public readonly ?string $region;

    public readonly ?string $postalCode;

    public readonly ?string $countryCodeAlpha2;

    public readonly ?string $taxId;
}
