<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Paywall;

use Routegroup\IngPay\Concerns\CarriesIdentity;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Paywall\CreateTransactionRequestFactory;
use Routegroup\IngPay\Identified;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Value\Cart;
use Routegroup\IngPay\Value\Invoice;

/**
 * @method static CreateTransactionRequestFactory factory()
 */
class CreateTransactionRequest extends Payload implements Identified
{
    use CarriesIdentity;
    use HasFactory;

    public readonly ?int $amount;

    public readonly ?Currency $currency;

    public readonly ?string $orderId;

    public readonly ?string $customerId;

    public readonly ?string $customerFirstName;

    public readonly ?string $customerLastName;

    public readonly ?string $serviceId;

    public readonly ?string $merchantId;

    public readonly ?string $signature;

    public readonly ?string $customerEmail;

    public readonly ?string $customerPhone;

    public readonly ?string $urlSuccess;

    public readonly ?string $urlFailure;

    public readonly ?string $urlReturn;

    public readonly ?string $orderDescription;

    public readonly ?string $visibleMethod;

    public readonly ?int $validTo;

    /** Held as a value; the paywall channel encodes it when building the url. */
    public readonly ?Cart $cart;

    /** Held as a value; the paywall channel encodes it when building the url. */
    public readonly ?Invoice $invoice;
}
