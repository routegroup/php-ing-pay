<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Types;

enum CartItemState: string
{
    case NEW = 'NEW';
    case USED = 'USED';
}
