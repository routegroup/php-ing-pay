<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\PaymentProfileFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static PaymentProfileFactory factory()
 */
class PaymentProfile extends Payload
{
    use HasFactory;

    public readonly ?string $id;

    public readonly string $merchantMid;

    public readonly ?string $merchantCustomerId;

    public readonly ?string $firstName;

    public readonly ?string $lastName;

    public readonly ?string $email;

    public readonly ?string $maskedNumber;

    public readonly ?int $month;

    public readonly ?int $year;

    public readonly ?string $organization;

    public readonly ?bool $isActive;

    public readonly ?string $profile;

    public readonly ?string $firstTransactionId;
}
