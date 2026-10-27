<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\ServicePaymentMethodFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;

/**
 * @method static ServicePaymentMethodFactory factory()
 */
class ServicePaymentMethod extends Payload
{
    use HasFactory;

    public readonly ?PaymentMethod $paymentMethod;

    public readonly ?PaymentMethodCode $paymentMethodCode;

    public readonly ?bool $isActive;

    public readonly ?bool $isOnline;

    public readonly ?string $description;

    public readonly ?Currency $currency;

    public readonly ?TransactionLimits $transactionLimits;

    /** @var array<array-key, mixed>|null */
    public readonly ?array $paymentMethodImage;

    /** @var array<array-key, mixed>|null */
    public readonly ?array $paymentMethodCodeImage;
}
