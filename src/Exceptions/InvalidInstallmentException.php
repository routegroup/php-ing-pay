<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use InvalidArgumentException;

class InvalidInstallmentException extends InvalidArgumentException
{
    public function __construct(public readonly mixed $period, string $reason)
    {
        parent::__construct("installment.period: $reason");
    }

    /** @return array{period: mixed} */
    public function context(): array
    {
        return ['period' => $this->period];
    }
}
