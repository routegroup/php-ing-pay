<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests;

use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Response\BaseResponse;

/**
 * @param  'Value'|'Response'|'Notification'  $kind
 * @return list<class-string<Payload>>
 */
function payloadsIn(string $kind): array
{
    $found = [];

    foreach (glob(__DIR__.'/../src/'.$kind.'/*.php') ?: [] as $file) {
        $name = basename($file, '.php');

        if ($name === 'BaseResponse') {
            continue;
        }

        /** @var class-string<Payload> $class */
        $class = 'Routegroup\\IngPay\\'.$kind.'\\'.$name;
        $found[] = $class;
    }

    return $found;
}

it('has found every payload there is', function (): void {
    expect(payloadsIn('Value'))->toHaveCount(35)
        ->and(payloadsIn('Response'))->toHaveCount(15)
        ->and(payloadsIn('Notification'))->toHaveCount(3);
});

it('builds a value through a factory resolved by convention', function (string $payload): void {
    expect($payload::factory()->make() instanceof $payload)->toBeTrue();
})->with([...payloadsIn('Value'), ...payloadsIn('Notification')]);

it('survives a round trip through its own array form', function (string $payload): void {
    $built = $payload::factory()->make();

    /** @var Payload $rebuilt */
    $rebuilt = new $payload($built->toArray());

    expect($rebuilt->toArray())->toBe($built->toArray());
})->with([...payloadsIn('Value'), ...payloadsIn('Notification')]);

it('builds a varied set rather than one row repeated', function (string $payload): void {
    $arrays = array_map(
        fn (Payload $one): string => serialize($one->toArray()),
        $payload::factory()->makeMany(8),
    );

    expect(count(array_unique($arrays)))->toBeGreaterThan(1);
})->with([...payloadsIn('Value'), ...payloadsIn('Notification')]);

it('builds a response that knows it succeeded', function (string $payload): void {
    $built = $payload::factory()->make();

    expect($built)->toBeInstanceOf(BaseResponse::class)
        ->and($built->successful())->toBeTrue()
        ->and($built->failed())->toBeFalse()
        ->and($built->error())->toBeNull()
        ->and($built->http()->status)->toBe(200);
})->with(array_values(array_filter(
    payloadsIn('Response'),
    fn (string $class): bool => is_subclass_of($class, BaseResponse::class),
)));

it('builds a response that knows it failed, and says why', function (string $payload): void {
    $built = $payload::factory()->status(422)->make();

    expect($built->failed())->toBeTrue()
        ->and($built->successful())->toBeFalse()
        ->and($built->error())->not->toBeNull();
})->with(array_values(array_filter(
    payloadsIn('Response'),
    fn (string $class): bool => is_subclass_of($class, BaseResponse::class),
)));
