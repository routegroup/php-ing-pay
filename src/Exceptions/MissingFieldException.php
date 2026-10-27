<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use UnexpectedValueException;

class MissingFieldException extends UnexpectedValueException
{
    public function __construct(
        public readonly string $payload,
        public readonly string $field,
        public readonly string $expected,
    ) {
        parent::__construct(
            sprintf('%s requires [%s] of type %s, which the given data does not carry.', $payload, $field, $expected)
        );
    }

    /** @return array{payload: string, field: string, expected: string} */
    public function context(): array
    {
        return [
            'payload' => $this->payload,
            'field' => $this->field,
            'expected' => $this->expected,
        ];
    }
}
