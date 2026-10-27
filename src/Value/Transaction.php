<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\TransactionFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Types\TransactionSource;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Types\TransactionType;

/**
 * @method static TransactionFactory factory()
 */
class Transaction extends Payload
{
    use HasFactory;

    public readonly string $id;

    public readonly ?TransactionType $type;

    public readonly ?TransactionStatus $status;

    public readonly ?TransactionSource $source;

    public readonly int $created;

    public readonly int $modified;

    public readonly ?string $notificationUrl;

    public readonly string $serviceId;

    public readonly int $amount;

    public readonly ?Currency $currency;

    /** Not every transaction carries one, despite the old docblock claiming otherwise. */
    public readonly ?string $title;

    public readonly string $orderId;

    public readonly ?PaymentMethod $paymentMethod;

    public readonly ?PaymentMethodCode $paymentMethodCode;

    public readonly ?PaymentSummary $payment;

    public readonly ?PaymentProfile $paymentProfile;

    /** @var array<array-key, mixed>|null The specification defines no shape for a refund entry. */
    public readonly ?array $refunds;

    public readonly ?string $blikTransactionId;

    public readonly ?string $statusCode;

    public readonly ?string $statusCodeDescription;

    public readonly ?Fee $fee;

    public readonly ?PayerData $payerData;

    public readonly ?PayerCardData $payerCardData;

    public readonly ?int $paidAmount;

    public readonly ?Action $action;
}
