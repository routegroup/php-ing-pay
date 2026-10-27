<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Http\Response as HttpResponse;
use Routegroup\IngPay\Payload;

/**
 * A payload hydrated from what the gateway answered, keeping the answer itself
 * so a caller can ask about the status rather than guessing from the fields.
 */
abstract class BaseResponse extends Payload
{
    public function __construct(protected readonly HttpResponse $http)
    {
        parent::__construct($this->normalise($this->http->json()));
    }

    /**
     * Most endpoints answer with the object this payload describes; a few wrap
     * or list it. Those override this.
     *
     * @param  array<array-key, mixed>  $body
     * @return array<array-key, mixed>
     */
    protected function normalise(array $body): array
    {
        return $body;
    }

    public function http(): HttpResponse
    {
        return $this->http;
    }

    public function successful(): bool
    {
        return $this->http->successful();
    }

    public function failed(): bool
    {
        return $this->http->failed();
    }

    public function error(): ?ApiErrorResponse
    {
        return $this->failed() ? ApiErrorResponse::fromResponse($this->http) : null;
    }
}
