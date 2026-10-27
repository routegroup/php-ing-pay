<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Api;

use Routegroup\IngPay\Concerns\CarriesIdentity;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Api\ChargeProfileRequestFactory;
use Routegroup\IngPay\Identified;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Value\Invoice;

/**
 * @method static ChargeProfileRequestFactory factory()
 */
class ChargeProfileRequest extends Payload implements Identified
{
    use CarriesIdentity;
    use HasFactory;

    public readonly ?string $serviceId;

    public readonly ?string $paymentProfileId;

    public readonly ?int $amount;

    public readonly ?Currency $currency;

    public readonly ?string $orderId;

    public readonly ?string $title;

    public readonly ?string $notificationUrl;

    /** @var array<array-key, mixed>|null */
    public readonly ?array $data;

    public readonly ?Invoice $invoice;

    /** @var array<array-key, mixed>|null */
    public readonly ?array $multipayout;
}
