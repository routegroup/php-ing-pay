<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\ProfileResponseFactory;
use Routegroup\IngPay\Value\PaymentProfile;

/**
 * @method static ProfileResponseFactory factory()
 */
class ProfileResponse extends BaseResponse
{
    use HasFactory;

    public readonly ?PaymentProfile $paymentProfile;
}
