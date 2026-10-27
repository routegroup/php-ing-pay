<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\PaymentResponseFactory;
use Routegroup\IngPay\Value\Payment;

/**
 * @method static PaymentResponseFactory factory()
 */
class PaymentResponse extends BaseResponse
{
    use HasFactory;

    public readonly Payment $payment;
}
