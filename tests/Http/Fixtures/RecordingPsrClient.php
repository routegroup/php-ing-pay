<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Http\Fixtures;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RecordingPsrClient implements ClientInterface
{
    public ?RequestInterface $seen = null;

    public function __construct(private readonly ResponseInterface $answer) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->seen = $request;

        return $this->answer;
    }
}
