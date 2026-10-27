<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use InvalidArgumentException;

class InvalidInstallmentException extends InvalidArgumentException
{
    public readonly mixed $period;

    public function __construct(mixed $period, string $reason)
    {
        $this->period = $period;

        parent::__construct("installment.period: $reason");
    }

    /** @return array{period: mixed} */
    public function context(): array
    {
        return ['period' => $this->period];
    }
}
