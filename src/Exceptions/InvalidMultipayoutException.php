<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use InvalidArgumentException;

class InvalidMultipayoutException extends InvalidArgumentException
{
    public function __construct(public readonly int $index, public readonly string $field, string $reason)
    {
        parent::__construct("multipayout[{$this->index}][{$this->field}]: $reason");
    }

    /** @return array{index: int, field: string} */
    public function context(): array
    {
        return [
            'index' => $this->index,
            'field' => $this->field,
        ];
    }
}
