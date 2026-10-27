<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Api;

use Routegroup\IngPay\Concerns\CarriesIdentity;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Api\RefundRequestFactory;
use Routegroup\IngPay\Identified;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\TransactionType;

/**
 * @method static RefundRequestFactory factory()
 */
class RefundRequest extends Payload implements Identified
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
        parent::__construct(['type' => TransactionType::REFUND, ...(array) $attributes]);
    }

    public readonly ?int $amount;

    public readonly ?TransactionType $type;

    public readonly ?string $serviceId;

    public readonly ?string $title;

    public readonly ?bool $sendRefundConfirmationEmail;
}
