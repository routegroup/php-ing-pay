<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\ProfilesResponseFactory;
use Routegroup\IngPay\Value\PaymentProfile;

/**
 * @method static ProfilesResponseFactory factory()
 */
class ProfilesResponse extends BaseResponse
{
    use HasFactory;

    /** @var list<PaymentProfile>|null */
    public readonly ?array $paymentProfiles;
}
