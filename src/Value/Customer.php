<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\CustomerFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\Lang;

/**
 * @method static CustomerFactory factory()
 */
class Customer extends Payload
{
    use HasFactory;

    public readonly string $firstName;

    public readonly string $lastName;

    public readonly ?string $email;

    public readonly ?string $cid;

    public readonly ?string $company;

    public readonly ?string $phone;

    public readonly ?Lang $locale;
}
