<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Lets anyone who already has a PSR-18 client use it, without PSR-7 reaching
 * the rest of the package. Needs psr/http-client and psr/http-factory, which
 * this package suggests rather than requires.
 */
class Psr18Client implements Client
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requests,
        private readonly StreamFactoryInterface $streams,
    ) {}

    public function send(Request $request): Response
    {
        $psr = $this->requests->createRequest($request->method, $request->url)
            ->withHeader('Accept', 'application/json');

        foreach ($request->headers as $name => $value) {
            $psr = $psr->withHeader($name, $value);
        }

        if ($request->body !== []) {
            $psr = $psr
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->streams->createStream(
                    json_encode($request->body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                ));
        }

        $answer = $this->client->sendRequest($psr);

        $headers = [];

        foreach (array_keys($answer->getHeaders()) as $name) {
            $headers[$name] = $answer->getHeaderLine($name);
        }

        return new Response($answer->getStatusCode(), (string) $answer->getBody(), $headers);
    }
}
