<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\TrustedIpsResponseFactory;

/**
 * @method static TrustedIpsResponseFactory factory()
 */
class TrustedIpsResponse extends BaseResponse
{
    use HasFactory;

    /** @var array<array-key, mixed>|null */
    public readonly ?array $trustedIps;

    public readonly ?string $status;
}
