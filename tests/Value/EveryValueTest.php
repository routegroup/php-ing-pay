<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Value;

use Routegroup\IngPay\Payload;

/**
 * Covers every value in one sweep: each must have a factory that resolves by
 * convention, must build, must survive a round trip through its own array
 * form, and must refuse to be modified afterwards.
 *
 * @return list<class-string<Payload>>
 */
function everyValue(): array
{
    $values = [];

    foreach (glob(__DIR__.'/../../src/Value/*.php') ?: [] as $file) {
        /** @var class-string<Payload> $class */
        $class = 'Routegroup\\IngPay\\Value\\'.basename($file, '.php');
        $values[] = $class;
    }

    return $values;
}

it('covers every value in src/Value', function (): void {
    expect(everyValue())->toHaveCount(34);
});

it('builds through a factory resolved by convention', function (string $value): void {
    $built = $value::factory()->make();

    expect($built instanceof $value)->toBeTrue();
})->with(everyValue());

it('survives a round trip through its own array form', function (string $value): void {
    $built = $value::factory()->make();

    /** @var Payload $rebuilt */
    $rebuilt = new $value($built->toArray());

    expect($rebuilt->toArray())->toBe($built->toArray());
})->with(everyValue());

it('builds a varied set rather than one row repeated', function (string $value): void {
    $arrays = array_map(
        fn (Payload $payload): string => serialize($payload->toArray()),
        $value::factory()->makeMany(8),
    );

    expect(count(array_unique($arrays)))->toBeGreaterThan(1);
})->with(everyValue());
