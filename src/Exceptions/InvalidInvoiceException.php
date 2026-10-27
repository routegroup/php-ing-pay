<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use InvalidArgumentException;

class InvalidInvoiceException extends InvalidArgumentException
{
    public readonly string $field;

    public function __construct(string $field, string $reason)
    {
        $this->field = $field;

        parent::__construct("invoice.$field: $reason");
    }

    /** @return array{field: string} */
    public function context(): array
    {
        return ['field' => $this->field];
    }
}
