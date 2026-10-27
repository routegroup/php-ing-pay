<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Http;

use UnexpectedValueException;

/**
 * What the gateway answered, independent of what sent the request. This is the
 * type the model hydrates from, so nothing in it may come from a framework.
 */
class Response
{
    /** @param array<string, string> $headers */
    public function __construct(
        public readonly int $status,
        public readonly string $body,
        public readonly array $headers = [],
    ) {}

    public function successful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    public function failed(): bool
    {
        return ! $this->successful();
    }

    public function header(string $name): ?string
    {
        foreach ($this->headers as $key => $value) {
            if (strcasecmp($key, $name) === 0) {
                return $value;
            }
        }

        return null;
    }

    /**
     * An empty body decodes to an empty array: the gateway answers 404 with
     * nothing at all, and that is a shape, not a failure to parse.
     *
     * @return array<string, mixed>
     */
    public function json(): array
    {
        if (trim($this->body) === '') {
            return [];
        }

        $decoded = json_decode($this->body, true);

        if (! is_array($decoded)) {
            throw new UnexpectedValueException(
                sprintf('The gateway answered %d with a body that is not a JSON object.', $this->status)
            );
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }
}
