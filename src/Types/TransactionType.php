<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Types;

enum TransactionType: string
{
    case REFUND = 'refund';
    case SALE = 'sale';
}
