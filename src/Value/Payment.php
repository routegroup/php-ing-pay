<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\PaymentFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\Currency;

/**
 * @method static PaymentFactory factory()
 */
class Payment extends Payload
{
    use HasFactory;

    public readonly string $serviceId;

    public readonly int $amount;

    public readonly ?Currency $currency;

    public readonly string $orderId;

    public readonly Customer $customer;

    public readonly string $id;

    public readonly string $url;

    public readonly ?string $title;

    public readonly ?string $simp;

    public readonly ?string $notificationUrl;

    public readonly ?string $returnUrl;

    public readonly ?string $failureReturnUrl;

    public readonly ?string $successReturnUrl;

    public readonly bool $isActive;

    public readonly ?int $validTo;

    public readonly int $created;

    public readonly int $modified;
}
