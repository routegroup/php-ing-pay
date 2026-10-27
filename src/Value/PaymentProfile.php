<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Payload;

class PaymentProfile extends Payload
{
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
