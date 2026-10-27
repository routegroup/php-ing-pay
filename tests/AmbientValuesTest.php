<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\Transaction;

afterEach(function (): void {
    Factory::resolveAmbientUsing(null);
});

it('leaves the definition alone when nothing resolves ambient values', function (): void {
    $first = Transaction::factory()->make()->serviceId;
    $second = Transaction::factory()->make()->serviceId;

    expect($first)->not->toBe($second);
});

it('bakes ambient values into everything a factory builds', function (): void {
    Factory::resolveAmbientUsing(fn (): array => ['serviceId' => 'service-from-config']);

    expect(Transaction::factory()->make()->serviceId)->toBe('service-from-config')
        ->and(Transaction::factory()->makeMany(3))->each(
            fn ($transaction) => $transaction->serviceId->toBe('service-from-config')
        );
});

it('lets a state win over an ambient value', function (): void {
    Factory::resolveAmbientUsing(fn (): array => ['serviceId' => 'service-from-config']);

    expect(Transaction::factory()->state(['serviceId' => 'overridden'])->make()->serviceId)
        ->toBe('overridden');
});

it('lets the call site win over both', function (): void {
    Factory::resolveAmbientUsing(fn (): array => ['serviceId' => 'service-from-config']);

    expect(Transaction::factory()->state(['serviceId' => 'overridden'])->make(['serviceId' => 'explicit'])->serviceId)
        ->toBe('explicit');
});

it('ignores ambient keys a payload does not declare', function (): void {
    Factory::resolveAmbientUsing(fn (): array => [
        'serviceId' => 'service-from-config',
        'merchantId' => 'merchant-from-config',
    ]);

    expect(Transaction::factory()->make()->toArray())
        ->toHaveKey('serviceId')
        ->not->toHaveKey('merchantId');
});

it('is resolved afresh for every payload, not captured once', function (): void {
    $calls = 0;
    Factory::resolveAmbientUsing(function () use (&$calls): array {
        $calls++;

        return ['serviceId' => 'call-'.$calls];
    });

    $built = Transaction::factory()->makeMany(3);

    expect(array_map(fn (Transaction $t): string => $t->serviceId, $built))
        ->toBe(['call-1', 'call-2', 'call-3']);
});

it('stops applying them once cleared', function (): void {
    Factory::resolveAmbientUsing(fn (): array => ['serviceId' => 'service-from-config']);
    Factory::resolveAmbientUsing(null);

    expect(Transaction::factory()->make()->serviceId)->not->toBe('service-from-config');
});
