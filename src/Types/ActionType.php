<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Types;

enum ActionType: string
{
    case REDIRECT = 'redirect';
    case TRANSFER = 'transfer';
}
