<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\TransactionStatus;

class PaymentSummary extends Payload
{
    public readonly string $id;

    public readonly ?TransactionStatus $status;
}
