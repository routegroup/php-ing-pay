<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use InvalidArgumentException;

/** Covers the whole wallet flow, browser data included, so the field carries its own full path. */
class InvalidWalletException extends InvalidArgumentException
{
    public function __construct(public readonly string $field, string $reason)
    {
        parent::__construct("{$this->field}: $reason");
    }

    /** @return array{field: string} */
    public function context(): array
    {
        return ['field' => $this->field];
    }
}
