<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\ChargeProfileResponseFactory;
use Routegroup\IngPay\Value\Transaction;

/**
 * @method static ChargeProfileResponseFactory factory()
 */
class ChargeProfileResponse extends BaseResponse
{
    use HasFactory;

    public readonly Transaction $transaction;
}
