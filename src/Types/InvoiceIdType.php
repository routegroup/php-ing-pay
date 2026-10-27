<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Types;

enum InvoiceIdType: string
{
    case ID = 'ID';
    case VAT_ID = 'VAT_ID';
}
