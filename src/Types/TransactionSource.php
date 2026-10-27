<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Types;

enum TransactionSource: string
{
    case API = 'api';
    case WEB = 'web';
}
