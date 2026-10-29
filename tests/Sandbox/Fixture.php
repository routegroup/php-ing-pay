<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Sandbox;

use RuntimeException;

/**
 * Rewrites a caught notification as one safe to publish.
 *
 * Verification is hash(body . serviceKey), so a real body beside its real
 * signature is an offline oracle for the key. The merchant, service and
 * notification URL become fixture values and the signature is recomputed under
 * a key that exists only in the test suite.
 */
final class Fixture
{
    public const string MERCHANT_ID = 'fixturemerchant00001';

    public const string SERVICE_ID = '00000000-0000-4000-8000-000000000001';

    public const string SERVICE_KEY = 'fixture-service-key';

    public const string NOTIFICATION_URL = 'https://example.test/notify';

    /** @return list<string> the names written, without the directory or the extension */
    public static function writeAll(string $into): array
    {
        if (Sandbox::missing() !== []) {
            throw new RuntimeException('Nothing to scrub for: '.Sandbox::NO_CREDENTIALS);
        }

        if (! is_dir($into) && ! mkdir($into, 0o775, true) && ! is_dir($into)) {
            throw new RuntimeException('Could not create '.$into.'.');
        }

        $written = [];

        foreach (Notifications::caught() as $caught) {
            $body = self::scrub($caught['body']);
            $base = self::name($caught['payload']);
            $name = $base;

            for ($n = 2; in_array($name, $written, true); $n++) {
                $name = $base.'-'.$n;
            }

            file_put_contents($into.'/'.$name.'.json', json_encode([
                'headers' => ['x-imoje-signature' => self::signature($body)],
                'body' => $body,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n");

            $written[] = $name;
        }

        return $written;
    }

    public static function scrub(string $body): string
    {
        foreach ([
            'ING_PAY_MERCHANT_ID' => self::MERCHANT_ID,
            'ING_PAY_SERVICE_ID' => self::SERVICE_ID,
        ] as $key => $fixture) {
            $real = Sandbox::value($key);

            if ($real !== null) {
                $body = str_replace($real, $fixture, $body);
            }
        }

        $body = (string) preg_replace(
            '/"notificationUrl":"[^"]*"/',
            '"notificationUrl":"'.self::NOTIFICATION_URL.'"',
            $body,
        );

        foreach (Sandbox::CREDENTIALS as $key) {
            $real = Sandbox::value($key);

            // Fails closed: a body carrying one of these must not reach an artifact.
            if ($real !== null && str_contains($body, $real)) {
                throw new RuntimeException($key.' survived the scrub.');
            }
        }

        return $body;
    }

    public static function signature(string $body): string
    {
        return implode(';', [
            'merchantid='.self::MERCHANT_ID,
            'serviceid='.self::SERVICE_ID,
            'signature='.hash('sha256', $body.self::SERVICE_KEY),
            'alg=sha256',
        ]);
    }

    /** @param array<array-key, mixed> $payload */
    private static function name(array $payload): string
    {
        $transaction = $payload['transaction'] ?? null;

        if (! is_array($transaction)) {
            return 'notification';
        }

        $parts = array_filter([
            $transaction['paymentMethodCode'] ?? null,
            $transaction['type'] ?? null,
            $transaction['status'] ?? null,
        ], static fn (mixed $part): bool => is_string($part) && $part !== '');

        return $parts === [] ? 'notification' : implode('-', $parts);
    }
}
