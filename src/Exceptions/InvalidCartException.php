<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use InvalidArgumentException;

class InvalidCartException extends InvalidArgumentException
{
    public function __construct(public readonly string $field, string $reason)
    {
        parent::__construct("cart.{$this->field}: $reason");
    }

    /** @return array{field: string} */
    public function context(): array
    {
        return ['field' => $this->field];
    }
}
