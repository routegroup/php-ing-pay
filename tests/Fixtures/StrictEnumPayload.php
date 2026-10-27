<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Fixtures;

use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\TransactionStatus;

class StrictEnumPayload extends Payload
{
    protected bool $strictEnums = true;

    public readonly ?TransactionStatus $status;
}
