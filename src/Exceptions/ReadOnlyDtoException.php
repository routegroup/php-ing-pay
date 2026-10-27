<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use Exception;

class ReadOnlyDtoException extends Exception
{
    protected $message = 'This action is illegal.';
}
