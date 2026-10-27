<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Api;

use Routegroup\IngPay\Concerns\CarriesIdentity;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Api\PutTrustedIpsRequestFactory;
use Routegroup\IngPay\Identified;
use Routegroup\IngPay\Payload;

/**
 * @method static PutTrustedIpsRequestFactory factory()
 */
class PutTrustedIpsRequest extends Payload implements Identified
{
    use CarriesIdentity;
    use HasFactory;

    /** @var array<array-key, mixed> */
    public readonly ?array $trustedIps;
}
