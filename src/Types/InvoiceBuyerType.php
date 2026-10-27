<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Types;

enum InvoiceBuyerType: string
{
    case PERSON = 'PERSON';
    case COMPANY = 'COMPANY';
}
