<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Api;

use Routegroup\IngPay\Concerns\CarriesIdentity;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Api\DeactivateProfileRequestFactory;
use Routegroup\IngPay\Identified;
use Routegroup\IngPay\Payload;

/**
 * @method static DeactivateProfileRequestFactory factory()
 */
class DeactivateProfileRequest extends Payload implements Identified
{
    use CarriesIdentity;
    use HasFactory;

    public readonly ?string $paymentProfileId;
}
