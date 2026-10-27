<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use Exception;

class InvalidSignatureException extends Exception
{
    protected $message = 'Provided signature is invalid.';
}
