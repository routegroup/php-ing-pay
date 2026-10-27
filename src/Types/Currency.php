<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Types;

enum Currency: string
{
    case PLN = 'PLN';
    case EUR = 'EUR';
    case CZK = 'CZK';
    case GBP = 'GBP';
    case USD = 'USD';
    case HUF = 'HUF';
    case SEK = 'SEK';
    case RON = 'RON';
    case CHF = 'CHF';
    case BGN = 'BGN';

}
