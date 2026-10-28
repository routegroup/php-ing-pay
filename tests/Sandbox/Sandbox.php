<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Sandbox;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\Psr18Client;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Types\Environment;

/** Credentials come from .env locally and from the environment in CI. */
final class Sandbox
{
    public const int PORT = 8000;

    public const string NO_CREDENTIALS = 'sandbox credentials are not set; see .env.example';

    public const array CREDENTIALS = [
        'ING_PAY_MERCHANT_ID',
        'ING_PAY_SERVICE_ID',
        'ING_PAY_SERVICE_KEY',
        'ING_PAY_API_KEY',
    ];

    public static function value(string $key): ?string
    {
        static $file = null;

        if ($file === null) {
            $file = [];

            $path = self::root().'/.env';

            // Not @file(): PHPUnit still reports the suppressed warning.
            $lines = is_file($path) ? file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

            foreach ($lines ?: [] as $line) {
                if (str_starts_with(trim($line), '#') || ! str_contains($line, '=')) {
                    continue;
                }

                [$name, $value] = explode('=', $line, 2);
                $file[trim($name)] = trim($value, " \t\"'");
            }
        }

        $value = $file[$key] ?? getenv($key);

        return is_string($value) && $value !== '' ? $value : null;
    }

    /** @return list<string> */
    public static function missing(): array
    {
        return array_values(array_filter(self::CREDENTIALS, static fn (string $key): bool => self::value($key) === null));
    }

    public static function isConfigured(): bool
    {
        return self::missing() === [];
    }

    public static function config(): Config
    {
        return new Config(
            merchantId: self::value('ING_PAY_MERCHANT_ID') ?? '',
            serviceId: self::value('ING_PAY_SERVICE_ID') ?? '',
            serviceKey: self::value('ING_PAY_SERVICE_KEY') ?? '',
            apiKey: self::value('ING_PAY_API_KEY') ?? '',
            env: Environment::from(self::value('ING_PAY_ENV') ?? Environment::SANDBOX->value),
        );
    }

    public static function api(): Api
    {
        $config = self::config();

        return new Api(
            Psr18Client::guzzle(['timeout' => 30, 'connect_timeout' => 10, 'http_errors' => false]),
            $config,
            new Url($config),
        );
    }

    public static function root(): string
    {
        return dirname(__DIR__, 2);
    }

    public static function caught(): string
    {
        $path = self::root().'/.sandbox/caught';

        if (! is_dir($path)) {
            mkdir($path, 0o775, true);
        }

        return $path;
    }

    public static function orderId(string $prefix): string
    {
        return sprintf('%s-%s-%s', $prefix, self::value('GITHUB_RUN_ID') ?? 'local', bin2hex(random_bytes(3)));
    }
}
