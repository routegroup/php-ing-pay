<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Api;

use Routegroup\IngPay\Concerns\CarriesIdentity;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Api\CancelPaymentRequestFactory;
use Routegroup\IngPay\Identified;
use Routegroup\IngPay\Payload;

/**
 * @method static CancelPaymentRequestFactory factory()
 */
class CancelPaymentRequest extends Payload implements Identified
{
    use CarriesIdentity;
    use HasFactory;

    public readonly ?string $serviceId;

    public readonly ?string $paymentId;
}
