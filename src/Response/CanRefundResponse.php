<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\CanRefundResponseFactory;
use Routegroup\IngPay\Value\PartialRefund;

/**
 * @method static CanRefundResponseFactory factory()
 */
class CanRefundResponse extends BaseResponse
{
    use HasFactory;

    public readonly ?string $id;

    public readonly ?bool $refundable;

    public readonly ?bool $balance;

    public readonly ?int $fullRefund;

    public readonly ?PartialRefund $partialRefund;
}
