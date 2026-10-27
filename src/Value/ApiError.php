<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\ApiErrorFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static ApiErrorFactory factory()
 */
class ApiError extends Payload
{
    use HasFactory;

    public readonly ?string $property;

    public readonly ?string $message;
}
