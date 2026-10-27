<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Http;

/**
 * What the gateway is asked for, independent of how it is sent.
 */
class Request
{
    /**
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers
     */
    public function __construct(
        public readonly string $method,
        public readonly string $url,
        public readonly array $body = [],
        public readonly array $headers = [],
    ) {}

    /** @param array<string, mixed> $body */
    public static function post(string $url, array $body = []): self
    {
        return new self('POST', $url, $body);
    }

    public static function get(string $url): self
    {
        return new self('GET', $url);
    }

    public static function delete(string $url): self
    {
        return new self('DELETE', $url);
    }

    /** @param array<string, mixed> $body */
    public static function put(string $url, array $body = []): self
    {
        return new self('PUT', $url, $body);
    }

    /** @param array<string, string> $headers */
    public function withHeaders(array $headers): self
    {
        return new self($this->method, $this->url, $this->body, [...$this->headers, ...$headers]);
    }
}
