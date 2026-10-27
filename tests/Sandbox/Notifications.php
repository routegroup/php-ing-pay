<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Sandbox;

use RuntimeException;

/**
 * Reads what bin/sandbox-server.php caught.
 *
 * The gateway delivers to the one notification address configured on the
 * service, so a run holds the tunnel alone and everything that arrives while
 * it runs is its own.
 */
final class Notifications
{
    public static function clear(): void
    {
        foreach (glob(Sandbox::caught().'/*.json') ?: [] as $file) {
            unlink($file);
        }
    }

    /**
     * @param  callable(array<array-key, mixed>): bool  $matches
     * @return array{headers: array<string, string>, body: string, payload: array<array-key, mixed>}
     */
    public static function waitFor(callable $matches, string $describing, int $seconds = 60): array
    {
        $deadline = microtime(true) + $seconds;

        do {
            foreach (self::caught() as $caught) {
                if ($matches($caught['payload'])) {
                    return $caught;
                }
            }

            usleep(500_000);
        } while (microtime(true) < $deadline);

        throw new RuntimeException(sprintf(
            'Waited %ds for %s; %d notifications arrived.',
            $seconds,
            $describing,
            count(self::caught()),
        ));
    }

    /** @return list<array{headers: array<string, string>, body: string, payload: array<array-key, mixed>}> */
    public static function caught(): array
    {
        $caught = [];

        foreach (glob(Sandbox::caught().'/*.json') ?: [] as $file) {
            $contents = json_decode((string) file_get_contents($file), true);

            if (! is_array($contents) || ! is_string($contents['body'] ?? null)) {
                continue;
            }

            $payload = json_decode($contents['body'], true);

            /** @var array<string, string> $headers */
            $headers = is_array($contents['headers'] ?? null) ? $contents['headers'] : [];

            $caught[] = [
                'headers' => $headers,
                'body' => $contents['body'],
                'payload' => is_array($payload) ? $payload : [],
            ];
        }

        return $caught;
    }

    /** @param array<array-key, mixed> $payload */
    public static function isFor(array $payload, string $transactionId, ?string $status = null): bool
    {
        $transaction = $payload['transaction'] ?? null;

        if (! is_array($transaction) || ($transaction['id'] ?? null) !== $transactionId) {
            return false;
        }

        return $status === null || ($transaction['status'] ?? null) === $status;
    }
}
