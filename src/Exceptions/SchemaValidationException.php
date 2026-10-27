<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use Exception;

class SchemaValidationException extends Exception
{
    /** @var array<string, string> */
    public readonly array $errors;

    /** @param array<string, string> $errors */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
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
