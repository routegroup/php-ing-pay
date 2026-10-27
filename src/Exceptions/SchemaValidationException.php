<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use Exception;

class SchemaValidationException extends Exception
{
    /** @param array<string, string> $errors */
    public function __construct(public readonly array $errors)
    {
        parent::__construct('Given data doesnt fulfill JSON schema requirements.', 422);
    }

    /** @return array{errors: array<string, string>} */
    public function context(): array
    {
        return [
            'errors' => $this->errors,
        ];
    }
}
