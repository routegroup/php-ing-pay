<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Lib;

use JsonSchema\Validator as JsonSchemaValidator;
use Routegroup\IngPay\Exceptions\InvalidSignatureException;
use Routegroup\IngPay\Exceptions\SchemaValidationException;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Lib\Validator;
use Routegroup\IngPay\Notification\GenericNotification;
use Routegroup\IngPay\Types\Environment;
use RuntimeException;

function validatorConfig(): Config
{
    return new Config(
        merchantId: 'merchant-1',
        serviceId: 'service-1',
        serviceKey: 'secret-key',
        apiKey: 'api-key',
        env: Environment::SANDBOX,
    );
}

function validator(): Validator
{
    $config = validatorConfig();

    return new Validator(new JsonSchemaValidator, new Utils($config), $config);
}

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function notification(array $overrides = []): array
{
    return ['transaction' => array_merge([
        'id' => 'tx-1',
        'amount' => 12900,
        'currency' => 'PLN',
        'status' => 'settled',
        'orderId' => 'ORDER-1',
        'serviceId' => 'service-1',
        'type' => 'sale',
    ], $overrides)];
}

function signedHeader(string $body, string $alg = 'sha256', string $merchant = 'merchant-1'): string
{
    return implode(';', [
        "merchantid={$merchant}",
        'serviceid=service-1',
        'signature='.hash($alg, $body.'secret-key'),
        "alg={$alg}",
    ]);
}

it('accepts a notification carrying a complete transaction', function (): void {
    expect(validator()->fromNotification(notification()))->toBeTrue();
});

it('accepts a notification carrying any one of the four documented objects', function (array $body): void {
    expect(validator()->fromNotification($body))->toBeTrue();
})->with([
    'payment' => [['payment' => ['status' => 'settled']]],
    'action' => [['action' => ['type' => 'redirect']]],
    'paymentProfile' => [['paymentProfile' => ['id' => 'profile-1']]],
]);

it('refuses a body carrying none of them', function (): void {
    expect(fn (): bool => validator()->fromNotification(['somethingElse' => []]))
        ->toThrow(SchemaValidationException::class);
});

it('refuses a transaction missing a field the gateway always sends', function (string $field): void {
    $data = notification();
    unset($data['transaction'][$field]);

    expect(fn (): bool => validator()->fromNotification($data))->toThrow(SchemaValidationException::class);
})->with(['id', 'amount', 'currency', 'status', 'orderId', 'serviceId', 'type']);

it('refuses a value outside the enum it belongs to', function (string $field, string $value): void {
    expect(fn (): bool => validator()->fromNotification(notification([$field => $value])))
        ->toThrow(SchemaValidationException::class);
})->with([['currency', 'XXX'], ['status', 'teleported'], ['type', 'barter']]);

it('refuses an amount that is not positive', function (int $amount): void {
    expect(fn (): bool => validator()->fromNotification(notification(['amount' => $amount])))
        ->toThrow(SchemaValidationException::class);
})->with([0, -1]);

it('leaves an unknown key alone rather than rejecting the payload', function (): void {
    $data = notification();
    $data['somethingIngAddedLastTuesday'] = ['x' => 1];

    expect(validator()->fromNotification($data))->toBeTrue();
});

it('names the schema and the offending property when it refuses', function (): void {
    try {
        validator()->fromNotification(notification(['currency' => 'XXX']));
    } catch (SchemaValidationException $exception) {
        expect($exception->errors['schema'])->toBe('notification')
            ->and($exception->errors)->toHaveKey('transaction.currency');

        return;
    }

    throw new RuntimeException('SchemaValidationException was not thrown.');
});

it('accepts a signature it can recompute', function (): void {
    $body = json_encode(notification(), JSON_THROW_ON_ERROR);

    validator()->verifySignature($body, signedHeader($body));
})->throwsNoExceptions();

it('accepts the headers Utils builds, which closes the loop', function (): void {
    $config = validatorConfig();
    $utils = new Utils($config);
    $payload = GenericNotification::factory()->make();
    $body = json_encode($payload->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

    new Validator(new JsonSchemaValidator, $utils, $config)
        ->verifySignature($body, $utils->mockHeaders($payload)['x-imoje-signature']);
})->throwsNoExceptions();

it('refuses an unsigned or tampered notification', function (string $body, string $header): void {
    expect(fn () => validator()->verifySignature($body, $header))
        ->toThrow(InvalidSignatureException::class);
})->with([
    'no header at all' => ['{"a":1}', ''],
    'unknown algorithm' => ['{"a":1}', 'merchantid=merchant-1;serviceid=service-1;signature=x;alg=rot13'],
    'digest does not match' => ['{"a":1}', 'merchantid=merchant-1;serviceid=service-1;signature=deadbeef;alg=sha256'],
]);

it('refuses an empty body even when the empty string is signed correctly', function (): void {
    // Without the guard the digest of '' matches, so a replayed empty body
    // would be accepted on a signature that proves nothing about intent.
    expect(fn () => validator()->verifySignature('', signedHeader('')))
        ->toThrow(InvalidSignatureException::class);
});

it('refuses a signature issued for another merchant', function (): void {
    $body = '{"a":1}';

    expect(fn () => validator()->verifySignature($body, signedHeader($body, merchant: 'someone-else')))
        ->toThrow(InvalidSignatureException::class);
});
