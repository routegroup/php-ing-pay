<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Payload;

class Fee extends Payload
{
    public readonly ?int $merchant;

    public readonly ?int $surcharge;
}
