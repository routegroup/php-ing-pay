<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\RefundResponseFactory;
use Routegroup\IngPay\Value\Transaction;

/**
 * @method static RefundResponseFactory factory()
 */
class RefundResponse extends BaseResponse
{
    use HasFactory;

    public readonly Transaction $transaction;
}
