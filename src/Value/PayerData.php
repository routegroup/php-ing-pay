<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Payload;

class PayerData extends Payload
{
    public readonly ?string $name;

    public readonly ?string $account;
}
