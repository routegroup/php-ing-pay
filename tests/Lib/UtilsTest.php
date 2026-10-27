<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Lib;

use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\Environment;
use Routegroup\IngPay\Types\HashMethod;
use Routegroup\IngPay\Value\Fee;

function utils(): Utils
{
    return new Utils(new Config(
        merchantId: 'merchant-1',
        serviceId: 'service-1',
        serviceKey: 'secret-key',
        apiKey: 'api-key',
        env: Environment::SANDBOX,
    ));
}

/** @return array<string, mixed> */
function order(): array
{
    return [
        'serviceId' => 'service-1',
        'amount' => 12900,
        'currency' => 'PLN',
        'orderId' => 'ORDER-1',
        'title' => 'Zażółć gęślą jaźń',
        'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska', 'email' => null],
        'urls' => ['success' => 'https://example.com/s', 'failure' => 'https://example.com/f'],
    ];
}

/**
 * The expected values below were produced by the package this one is split out
 * of, so a difference here is a change in what the gateway would be sent to
 * verify — not a change in taste.
 */
it('builds the query exactly as the package it replaces did', function (): void {
    expect(utils()->buildQuery(order()))->toBe(
        'amount=12900&currency=PLN&customer[firstName]=Anna&customer[lastName]=Kowalska'
        .'&orderId=ORDER-1&serviceId=service-1&title=Zażółć gęślą jaźń'
        .'&urls[failure]=https://example.com/f&urls[success]=https://example.com/s'
    );
});

it('signs exactly as the package it replaces did', function (): void {
    expect(utils()->createSignature(order()))
        ->toBe('206b7cbf8063b12d91192edc0fbe5cb5c63ce623c90196643caec404c0f78143;sha256');
});

it('sorts by key at every level, not only the top', function (): void {
    expect(utils()->buildQuery(['b' => 1, 'a' => ['z' => 2, 'y' => 3]]))
        ->toBe('a[y]=3&a[z]=2&b=1');
});

it('skips a null rather than signing a parameter the gateway never receives', function (): void {
    expect(utils()->buildQuery(['a' => 1, 'b' => null, 'c' => 2]))->toBe('a=1&c=2');
});

it('verifies a signature it did not make', function (): void {
    $body = '{"transaction":{"id":"tx-1"}}';
    $signature = hash('sha256', $body.'secret-key');

    expect(utils()->verifySignature($signature, $body, HashMethod::SHA256))->toBeTrue()
        ->and(utils()->verifySignature($signature, $body.' ', HashMethod::SHA256))->toBeFalse()
        ->and(utils()->verifySignature('deadbeef', $body, HashMethod::SHA256))->toBeFalse();
});

it('flattens payloads and enums before signing', function (): void {
    expect(utils()->transformValues([
        'fee' => new Fee(['merchant' => 10, 'surcharge' => null]),
        'currency' => Currency::PLN,
        'plain' => 'x',
    ]))->toBe([
        'fee' => ['merchant' => 10],
        'currency' => 'PLN',
        'plain' => 'x',
    ]);
});

it('builds headers that look like the gateway it imitates', function (): void {
    $headers = utils()->mockHeaders(new Fee(['merchant' => 199]));

    expect($headers)->toHaveKey('x-imoje-signature')
        ->and($headers['x-imoje-signature'])->toContain('merchantid=merchant-1')
        ->and($headers['x-imoje-signature'])->toContain('serviceid=service-1')
        ->and($headers['x-imoje-signature'])->toContain('alg=sha256');
});
