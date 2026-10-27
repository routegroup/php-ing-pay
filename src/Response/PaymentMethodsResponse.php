<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Attributes\ListOf;
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
    #[ListOf(PaymentMethodGroup::class)]
    public readonly ?array $methods;

    public readonly ?int $timestamp;

    /**
     * The specification declares a bare list of methods. The gateway sends that
     * same list wrapped in {timestamp, data}. Accept either, so the documented
     * shape keeps working if ING ever drops the envelope.
     *
     * @param  array<array-key, mixed>  $body
     * @return array<array-key, mixed>
     */
    protected function normalise(array $body): array
    {
        $data = $body['data'] ?? null;

        $methods = match (true) {
            array_is_list($body) => $body,
            is_array($data) && array_is_list($data) => $data,
            default => [],
        };

        return [
            'methods' => $methods,
            'timestamp' => $body['timestamp'] ?? null,
        ];
    }
}
