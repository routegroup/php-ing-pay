<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs;

use Routegroup\IngPay\Exceptions\InvalidSignatureException;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Notification\GenericNotification;
use Routegroup\IngPay\Notification\RefundNotification;
use Routegroup\IngPay\Notification\ReportNotification;
use Routegroup\IngPay\Tests\Sandbox\Fixture;
use Routegroup\IngPay\Types\Environment;
use Routegroup\IngPay\Types\TransactionStatus;

/**
 * The samples on the Notifications page, fed the recorded notifications the
 * page shows, under the identity they were re-signed for.
 *
 * @return array{string, string}
 */
function recordedNotification(string $name): array
{
    /** @var array{headers: array<string, string>, body: string} $fixture */
    $fixture = json_decode((string) file_get_contents(__DIR__.'/../Fixtures/Notifications/'.$name.'.json'), true, flags: JSON_THROW_ON_ERROR);

    return [$fixture['body'], $fixture['headers']['x-imoje-signature']];
}

function recordedIdentity(): Config
{
    return new Config(
        merchantId: Fixture::MERCHANT_ID,
        serviceId: Fixture::SERVICE_ID,
        serviceKey: Fixture::SERVICE_KEY,
        apiKey: 'unused',
        env: Environment::SANDBOX,
    );
}

it('shows each body exactly as it was recorded', function (string $name): void {
    [$body] = recordedNotification($name);
    $shown = (string) file_get_contents(__DIR__.'/../Fixtures/Notifications/bodies/'.$name.'.json');

    expect(json_decode($shown, true))->toBe(json_decode($body, true));
})->with(['blik-settled', 'refund-settled', 'profile-charge-settled']);

it('verifies a sale notification, reads it and answers ok', function (): void {
    $config = recordedIdentity();
    [$body, $signature] = recordedNotification('blik-settled');

    $validator = require __DIR__.'/samples/notifications/verify.php';

    ob_start();
    $notification = require __DIR__.'/samples/notifications/handle.php';

    expect(ob_get_clean())->toBe('{"status":"ok"}')
        ->and($notification)->toBeInstanceOf(GenericNotification::class)
        ->and($notification->transaction?->status)->toBe(TransactionStatus::SETTLED);
});

it('reads a refund notification as a refund', function (): void {
    $config = recordedIdentity();
    [$body, $signature] = recordedNotification('refund-settled');

    $validator = require __DIR__.'/samples/notifications/verify.php';

    ob_start();
    $notification = require __DIR__.'/samples/notifications/handle.php';
    ob_end_clean();

    expect($notification)->toBeInstanceOf(RefundNotification::class)
        ->and($notification->originalTransactionId)->toBe($notification->payment->id);
});

it('refuses a body changed after it was signed', function (): void {
    $config = recordedIdentity();
    [$body, $signature] = recordedNotification('blik-settled');
    $body = str_replace('"amount":1000', '"amount":1', $body);

    require __DIR__.'/samples/notifications/verify.php';
})->throws(InvalidSignatureException::class);

it('reads a report notification without a signature', function (): void {
    $body = json_encode(ReportNotification::factory()->make(['fileExt' => 'csv'])->toArray(), JSON_THROW_ON_ERROR);

    $report = require __DIR__.'/samples/notifications/report.php';

    expect($report->fileExt)->toBe('csv')
        ->and($report->url)->not->toBeEmpty();
});

it('tests an endpoint with headers the gateway would send', function (): void {
    $config = docsConfig();

    require __DIR__.'/samples/notifications/testing.php';
});
