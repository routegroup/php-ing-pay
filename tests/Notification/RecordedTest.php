<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Notification;

use JsonSchema\Validator as JsonSchemaValidator;
use Routegroup\IngPay\Exceptions\InvalidSignatureException;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Lib\Validator;
use Routegroup\IngPay\Notification\GenericNotification;
use Routegroup\IngPay\Notification\RefundNotification;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\Environment;

/**
 * Bodies the sandbox really sent, scrubbed of the merchant they were issued
 * for and re-signed under a key that exists only here.
 */
function recordedConfig(): Config
{
    return new Config(
        merchantId: 'fixturemerchant00001',
        serviceId: '00000000-0000-4000-8000-000000000001',
        serviceKey: 'fixture-service-key',
        apiKey: 'unused',
        env: Environment::SANDBOX,
    );
}

function recordedValidator(): Validator
{
    $config = recordedConfig();

    return new Validator(new JsonSchemaValidator, new Utils($config), $config);
}

/** @return array{body: string, signature: string} */
function recorded(string $name): array
{
    $file = __DIR__.'/../Fixtures/Notifications/'.$name.'.json';

    /** @var array{headers: array<string, string>, body: string} $fixture */
    $fixture = json_decode((string) file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);

    return ['body' => $fixture['body'], 'signature' => $fixture['headers']['x-imoje-signature']];
}

/**
 * @param  array<array-key, mixed>  $sent
 * @param  array<array-key, mixed>  $kept
 * @return list<string>
 */
function droppedFields(array $sent, array $kept, string $path = ''): array
{
    $dropped = [];

    foreach ($sent as $key => $value) {
        $here = $path === '' ? (string) $key : $path.'.'.$key;

        // toArray() omits nulls by design, so a key the gateway sent as null
        // is not lost: the property is there and reads null.
        if (! array_key_exists($key, $kept)) {
            if ($value !== null) {
                $dropped[] = $here;
            }

            continue;
        }

        if (is_array($value) && is_array($kept[$key])) {
            $dropped = [...$dropped, ...droppedFields($value, $kept[$key], $here)];
        }
    }

    return $dropped;
}

dataset('recorded notifications', [
    'blik pending' => ['blik-pending', GenericNotification::class, 'sale', 'pending'],
    'blik settled' => ['blik-settled', GenericNotification::class, 'sale', 'settled'],
    'card pending' => ['card-pending', GenericNotification::class, 'sale', 'pending'],
    'card settled' => ['card-settled', GenericNotification::class, 'sale', 'settled'],
    'oneclick settled' => ['oneclick-settled', GenericNotification::class, 'sale', 'settled'],
    'profile charge' => ['profile-charge-settled', GenericNotification::class, 'sale', 'settled'],
    'refund new' => ['refund-new', RefundNotification::class, 'refund', 'new'],
    'refund settled' => ['refund-settled', RefundNotification::class, 'refund', 'settled'],
]);

it('accepts the signature the gateway sent', function (string $name): void {
    $fixture = recorded($name);

    expect(function () use ($fixture): void {
        recordedValidator()->verifySignature($fixture['body'], $fixture['signature']);
    })->not->toThrow(InvalidSignatureException::class);
})->with('recorded notifications');

it('passes the notification schema', function (string $name): void {
    $fixture = recorded($name);

    expect(recordedValidator()->fromNotification((array) json_decode($fixture['body'], true)))->toBeTrue();
})->with('recorded notifications');

it('hydrates into the class its shape calls for', function (string $name, string $class, string $type, string $status): void {
    $fixture = recorded($name);

    /** @var GenericNotification|RefundNotification $notification */
    $notification = new $class((array) json_decode($fixture['body'], true));

    expect($notification->transaction?->type?->value)->toBe($type)
        ->and($notification->transaction?->status?->value)->toBe($status);
})->with('recorded notifications');

it('keeps every field the gateway sent', function (string $name, string $class): void {
    $fixture = recorded($name);

    /** @var array<array-key, mixed> $sent */
    $sent = json_decode($fixture['body'], true);

    /** @var Payload $notification */
    $notification = new $class($sent);

    expect(droppedFields($sent, $notification->toArray()))->toBeEmpty();
})->with('recorded notifications');

it('carries the field only a refund notification has', function (): void {
    $fixture = recorded('refund-settled');

    $notification = new RefundNotification((array) json_decode($fixture['body'], true));

    expect($notification->originalTransactionId)->not->toBeEmpty();
});

it('rejects a body that was altered after signing', function (): void {
    $fixture = recorded('card-settled');

    recordedValidator()->verifySignature(
        str_replace('"amount"', '"amonut"', $fixture['body']),
        $fixture['signature'],
    );
})->throws(InvalidSignatureException::class);

it('drops what only the refund class declares, when read as a generic one', function (): void {
    $fixture = recorded('refund-settled');

    /** @var array<array-key, mixed> $sent */
    $sent = json_decode($fixture['body'], true);

    expect(droppedFields($sent, new GenericNotification($sent)->toArray()))
        ->toBe(['originalTransactionId']);
});
