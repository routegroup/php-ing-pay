<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Paywall;

use Routegroup\IngPay\Concerns\CarriesIdentity;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Paywall\InstallmentsCalculatorRequestFactory;
use Routegroup\IngPay\Identified;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\Currency;

/**
 * @method static InstallmentsCalculatorRequestFactory factory()
 */
class InstallmentsCalculatorRequest extends Payload implements Identified
{
    use CarriesIdentity;
    use HasFactory;

    /**
     * A default of the request itself, not of the caller's configuration.
     *
     * @param  array<array-key, mixed>  $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct(['currency' => Currency::PLN, ...(array) $attributes]);
    }

    public const MIN_AMOUNT = 30000;

    public const MAX_AMOUNT = 5000000;

    public const SCRIPT_ID = 'imoje-installments__script';

    public const WRAPPER_ID = 'imoje-installments__wrapper';

    public readonly ?string $merchantId;

    public readonly ?string $serviceId;

    public readonly ?int $amount;

    public readonly ?Currency $currency;

    public readonly ?string $signature;
}
