<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\PaymentMethodsResponseFactory;
use Routegroup\IngPay\Value\PaymentMethodGroup;

/**
 * @method static PaymentMethodsResponseFactory factory()
 */
class PaymentMethodsResponse extends BaseResponse
{
    use HasFactory;

    /** @var list<PaymentMethodGroup>|null */
    public readonly ?array $methods;

    public readonly ?int $timestamp;
}
