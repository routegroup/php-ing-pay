<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Attributes\ListOf;
use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\ApiErrorResponseFactory;
use Routegroup\IngPay\Http\Response as HttpResponse;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Value\ApiError;

/**
 * Most codes wrap the detail in an `apiErrorResponse` envelope; 400 and 401
 * arrive bare. Both are normalised here.
 *
 * @method static ApiErrorResponseFactory factory()
 */
class ApiErrorResponse extends Payload
{
    use HasFactory;

    public readonly ?string $code;

    public readonly ?string $message;

    public readonly mixed $instance;

    /** @var list<ApiError>|array<array-key, mixed> */
    #[ListOf(ApiError::class)]
    public readonly array $errors;

    public static function fromResponse(HttpResponse $response): self
    {
        $body = $response->json();
        $envelope = $body['apiErrorResponse'] ?? null;

        return new self(is_array($envelope) ? $envelope : $body);
    }

    /** @param array<array-key, mixed> $attributes */
    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;

        // A field the gateway omits still has to satisfy a non-nullable array.
        $attributes['errors'] = is_array($attributes['errors'] ?? null) ? $attributes['errors'] : [];

        parent::__construct($attributes);
    }
}
