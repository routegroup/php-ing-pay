<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Api;

use Routegroup\IngPay\Concerns\CarriesIdentity;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Api\CreateTransactionRequestFactory;
use Routegroup\IngPay\Identified;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Types\ProfileType;
use Routegroup\IngPay\Types\TransactionType;
use Routegroup\IngPay\Value\AdditionalData;
use Routegroup\IngPay\Value\Billing;
use Routegroup\IngPay\Value\Card;
use Routegroup\IngPay\Value\Customer;
use Routegroup\IngPay\Value\Installment;
use Routegroup\IngPay\Value\Invoice;
use Routegroup\IngPay\Value\Shipping;
use Routegroup\IngPay\Value\Wallet;

/**
 * @method static CreateTransactionRequestFactory factory()
 */
class CreateTransactionRequest extends Payload implements Identified
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
        parent::__construct(['type' => TransactionType::SALE, ...(array) $attributes]);
    }

    public readonly ?TransactionType $type;

    public readonly ?string $serviceId;

    public readonly ?int $amount;

    public readonly ?Currency $currency;

    public readonly ?string $orderId;

    public readonly ?PaymentMethod $paymentMethod;

    public readonly ?PaymentMethodCode $paymentMethodCode;

    public readonly ?string $successReturnUrl;

    public readonly ?string $failureReturnUrl;

    public readonly ?Customer $customer;

    public readonly ?string $title;

    public readonly ?Billing $billing;

    public readonly ?Shipping $shipping;

    public readonly ?Card $card;

    /** @var list<string>|null */
    public readonly ?array $visibleMethod;

    public readonly ?AdditionalData $additionalData;

    public readonly ?Wallet $wallet;

    /** Must be at least sixty seconds out; the gateway answers 422 below that. */
    public readonly ?int $validTo;

    public readonly ?string $notificationUrl;

    public readonly ?string $additionalDescription;

    public readonly ?string $blikCode;

    public readonly ?string $clientIp;

    public readonly ?ProfileType $profileType;

    /** @var array<array-key, mixed>|null */
    public readonly ?array $data;

    /** @var array<array-key, mixed> */
    public readonly ?array $multipayout;

    public readonly ?Installment $installment;

    public readonly ?Invoice $invoice;
}
