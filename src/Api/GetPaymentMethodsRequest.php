<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Api;

use Routegroup\IngPay\Concerns\CarriesIdentity;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Api\GetPaymentMethodsRequestFactory;
use Routegroup\IngPay\Identified;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\Currency;

/**
 * @method static GetPaymentMethodsRequestFactory factory()
 */
class GetPaymentMethodsRequest extends Payload implements Identified
{
    use CarriesIdentity;
    use HasFactory;

    public readonly ?int $amount;

    public readonly ?Currency $currency;

    public readonly ?string $device;

    public readonly ?string $locale;
}
