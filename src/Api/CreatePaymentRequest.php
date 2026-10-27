<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Api;

use Routegroup\IngPay\Concerns\CarriesIdentity;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Api\CreatePaymentRequestFactory;
use Routegroup\IngPay\Identified;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Value\Cart;
use Routegroup\IngPay\Value\Customer;

/**
 * @method static CreatePaymentRequestFactory factory()
 */
class CreatePaymentRequest extends Payload implements Identified
{
    use CarriesIdentity;
    use HasFactory;

    public readonly ?string $serviceId;

    public readonly ?int $amount;

    public readonly ?Currency $currency;

    public readonly ?string $orderId;

    public readonly ?Customer $customer;

    public readonly ?string $title;

    /** @var list<string>|null */
    public readonly ?array $visibleMethod;

    public readonly ?PaymentMethodCode $preselectMethodCode;

    public readonly ?string $returnUrl;

    public readonly ?string $successReturnUrl;

    public readonly ?string $failureReturnUrl;

    public readonly ?int $validTo;

    public readonly ?Cart $cart;
}
