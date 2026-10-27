<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\GetPaymentResponseFactory;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Customer;

/**
 * @method static GetPaymentResponseFactory factory()
 */
class GetPaymentResponse extends BaseResponse
{
    use HasFactory;

    public readonly string $id;

    public readonly string $url;

    public readonly string $serviceId;

    public readonly string $orderId;

    public readonly ?string $title;

    public readonly ?string $simp;

    public readonly int $amount;

    public readonly ?Currency $currency;

    public readonly ?TransactionStatus $status;

    public readonly bool $isActive;

    public readonly ?int $validTo;

    public readonly int $created;

    public readonly int $modified;

    public readonly bool $isGenerated;

    public readonly bool $isUsed;

    public readonly ?int $usedAt;

    public readonly bool $isConfirmVisited;

    public readonly ?int $confirmVisitedAt;

    public readonly ?string $returnUrl;

    public readonly ?string $failureReturnUrl;

    public readonly ?string $successReturnUrl;

    public readonly ?string $notificationUrl;

    public readonly Customer $customer;

    /** @var array<array-key, mixed> */
    public readonly array $transactions;
}
