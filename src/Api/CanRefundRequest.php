<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Api;

use Routegroup\IngPay\Concerns\CarriesIdentity;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Api\CanRefundRequestFactory;
use Routegroup\IngPay\Identified;
use Routegroup\IngPay\Payload;

/**
 * @method static CanRefundRequestFactory factory()
 */
class CanRefundRequest extends Payload implements Identified
{
    use CarriesIdentity;
    use HasFactory;

    public readonly ?int $amount;
}
