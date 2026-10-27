<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Fixtures;

use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Fee;

class LenientPayload extends Payload
{
    public readonly ?int $count;

    public readonly ?bool $flag;

    public readonly ?string $label;

    public readonly ?float $rate;

    public readonly ?TransactionStatus $status;

    public readonly ?Fee $fee;
}
