<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use RuntimeException;

class MissingFakerException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'The test factories need fakerphp/faker, which this package does not require at runtime. '
            .'Add it to your own require-dev: composer require --dev fakerphp/faker'
        );
    }
}
