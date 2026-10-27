<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use Exception;
use Routegroup\IngPay\Http\Response;
use Routegroup\IngPay\Response\ApiErrorResponse;

class ApiErrorException extends Exception
{
    public readonly ApiErrorResponse $error;

    /** @param array<array-key, mixed> $request */
    public function __construct(
        public readonly Response $response,
        public readonly array $request = [],
    ) {
        $this->error = ApiErrorResponse::fromResponse($response);

        parent::__construct(
            $this->error->message ?: ($response->reason ?: 'HTTP '.$response->status),
            $response->status,
        );
    }

    public function status(): int
    {
        return $this->response->status;
    }

    /** @return array{status: int, response: array<string, mixed>, request: array<array-key, mixed>} */
    public function context(): array
    {
        return [
            'status' => $this->status(),
            'response' => $this->error->toArray(),
            'request' => $this->request,
        ];
    }
}
