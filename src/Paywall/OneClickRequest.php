<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Paywall;

use Routegroup\IngPay\Concerns\CarriesIdentity;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Paywall\OneClickRequestFactory;
use Routegroup\IngPay\HasUnsignedFields;
use Routegroup\IngPay\Identified;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\Lang;
use Routegroup\IngPay\Types\WidgetType;

/**
 * @method static OneClickRequestFactory factory()
 */
class OneClickRequest extends Payload implements HasUnsignedFields, Identified
{
    use CarriesIdentity;
    use HasFactory;

    /** The widget refuses to initialise under any other id. */
    public const SCRIPT_ID = 'imoje-widget__script';

    /** Configuration of the widget itself, which the chapter excludes from the signature. */
    public const UNSIGNED = ['locale', 'inline', 'elementId', 'elementEvent'];

    /** The widget defaults to one-click; the gateway refuses a request without it. */
    protected bool $strictEnums = true;

    /** @param array<array-key, mixed> $attributes */
    public function __construct($attributes = [])
    {
        parent::__construct([
            'widgetType' => WidgetType::ONECLICK,
            ...(array) $attributes,
        ]);
    }

    /** @return list<string> */
    public function unsignedFields(): array
    {
        return self::UNSIGNED;
    }

    public readonly ?string $serviceId;

    public readonly ?string $merchantId;

    public readonly ?int $amount;

    public readonly ?Currency $currency;

    public readonly ?string $orderId;

    public readonly ?string $customerId;

    public readonly ?string $customerFirstName;

    public readonly ?string $customerLastName;

    public readonly ?string $customerEmail;

    public readonly ?string $signature;

    public readonly ?string $customerPhone;

    public readonly ?string $orderDescription;

    public readonly ?string $urlSuccess;

    public readonly ?string $urlFailure;

    public readonly ?string $urlReturn;

    public readonly ?string $urlCancel;

    public readonly ?WidgetType $widgetType;

    public readonly ?int $validTo;

    public readonly ?string $invoice;

    /** @var array<array-key, mixed>|null */
    public readonly ?array $multipayout;

    public readonly ?Lang $locale;

    public readonly ?bool $inline;

    public readonly ?string $elementId;

    public readonly ?string $elementEvent;
}
