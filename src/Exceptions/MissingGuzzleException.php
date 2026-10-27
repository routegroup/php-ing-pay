<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Exceptions;

use RuntimeException;

class MissingGuzzleException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'Psr18Client::guzzle() needs guzzlehttp/guzzle, which this package does not require. '
            .'Add it with: composer require guzzlehttp/guzzle — or pass any other PSR-18 client to the constructor.'
        );
    }
}
