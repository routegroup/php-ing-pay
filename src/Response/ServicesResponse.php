<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Attributes\ListOf;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\ServicesResponseFactory;
use Routegroup\IngPay\Value\ServiceData;

/**
 * @method static ServicesResponseFactory factory()
 */
class ServicesResponse extends BaseResponse
{
    use HasFactory;

    /** @var list<ServiceData>|null */
    #[ListOf(ServiceData::class)]
    public readonly ?array $services;

    /**
     * GET /services answers with a top-level list of {service} objects rather
     * than an object, confirmed against the sandbox.
     *
     * @param  array<array-key, mixed>  $body
     * @return array<array-key, mixed>
     */
    protected function normalise(array $body): array
    {
        return ['services' => array_map(
            static fn (mixed $item): mixed => is_array($item) && is_array($item['service'] ?? null)
                ? $item['service']
                : [],
            array_is_list($body) ? $body : [],
        )];
    }
}
