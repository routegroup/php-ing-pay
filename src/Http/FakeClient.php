<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Http;

use RuntimeException;

/**
 * Answers without a network, and keeps what it was asked.
 *
 * This is why the seam is ours rather than PSR-18: an integration can pin what
 * the gateway said and assert what was sent, in its own tests, without a
 * framework's help.
 */
class FakeClient implements Client
{
    /** @var list<Request> */
    private array $sent = [];

    /** @var list<Response> */
    private array $queued = [];

    public function __construct(private readonly ?Response $always = null) {}

    /** @param array<string, mixed> $body */
    public static function answering(array $body = [], int $status = 200): self
    {
        return new self(new Response($status, json_encode($body, JSON_THROW_ON_ERROR)));
    }

    /** @param array<string, mixed> $body */
    public function queue(array $body = [], int $status = 200): self
    {
        $this->queued[] = new Response($status, json_encode($body, JSON_THROW_ON_ERROR));

        return $this;
    }

    public function send(Request $request): Response
    {
        $this->sent[] = $request;

        if ($this->queued !== []) {
            return array_shift($this->queued);
        }

        return $this->always ?? throw new RuntimeException(
            sprintf('FakeClient has nothing left to answer %s %s with.', $request->method, $request->url)
        );
    }

    /** @return list<Request> */
    public function sent(): array
    {
        return $this->sent;
    }

    public function lastRequest(): ?Request
    {
        return $this->sent === [] ? null : $this->sent[count($this->sent) - 1];
    }

    public function sentCount(): int
    {
        return count($this->sent);
    }
}
