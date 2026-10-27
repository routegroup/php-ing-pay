<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use InvalidArgumentException;

class InvalidMultipayoutException extends InvalidArgumentException
{
    public readonly int $index;

    public readonly string $field;

    public function __construct(int $index, string $field, string $reason)
    {
        $this->index = $index;
        $this->field = $field;

        parent::__construct("multipayout[$index][$field]: $reason");
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
