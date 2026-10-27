<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Routegroup\IngPay\Attributes\ListOf;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Response\BaseResponse;
use SplFileInfo;

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

/** @return list<class-string<Payload>> */
function everyPayload(): array
{
    $root = __DIR__.'/../src';
    $found = [];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)) as $file) {
        if (! $file instanceof SplFileInfo || $file->getExtension() !== 'php') {
            continue;
        }

        /** @var class-string $class */
        $class = 'Routegroup\\IngPay\\'.str_replace('/', '\\', substr($file->getPathname(), strlen($root) + 1, -4));

        if (class_exists($class) && is_subclass_of($class, Payload::class) && ! new ReflectionClass($class)->isAbstract()) {
            /** @var class-string<Payload> $class */
            $found[] = $class;
        }
    }

    sort($found);

    return $found;
}

/** @return list<array{string, ReflectionProperty, class-string<Payload>|null, class-string|null}> */
function listFields(): array
{
    $fields = [];

    foreach (everyPayload() as $class) {
        foreach (new ReflectionClass($class)->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $type = $property->getType();

            if (! $type instanceof ReflectionNamedType || $type->getName() !== 'array') {
                continue;
            }

            $documented = documentedElement($property);
            $attributed = ($property->getAttributes(ListOf::class)[0] ?? null)?->newInstance()->type;

            if ($documented === null && $attributed === null) {
                continue;
            }

            $fields[] = [$class.'::$'.$property->getName(), $property, $documented, $attributed];
        }
    }

    return $fields;
}

/** @return class-string<Payload>|null */
function documentedElement(ReflectionProperty $property): ?string
{
    if (! preg_match('/(?:list|array)<\??(?:int|string|array-key)?,?\s*\??([\w\\\\]+)>/', $property->getDocComment() ?: '', $matches)) {
        return null;
    }

    $candidates = [
        $property->getDeclaringClass()->getNamespaceName().'\\'.$matches[1],
        'Routegroup\\IngPay\\Value\\'.$matches[1],
    ];

    foreach ($candidates as $candidate) {
        if (class_exists($candidate) && is_subclass_of($candidate, Payload::class)) {
            return $candidate;
        }
    }

    return null;
}

it('names the element type of every payload list in an attribute', function (): void {
    foreach (listFields() as [$where, , $documented, $attributed]) {
        expect($attributed)->not->toBeNull("{$where} documents a payload list but carries no ListOf")
            ->and($documented)->not->toBeNull("{$where} carries ListOf but documents no element type")
            ->and($attributed)->toBe($documented, "{$where}: attribute and docblock disagree");
    }

    // A sweep that examined nothing would pass in silence, and that is exactly
    // how this defect hid: opcache may strip the comments read here.
    expect(listFields())->toHaveCount(8);
});

it('hydrates a declared payload list into objects', function (string $payload): void {
    $built = $payload::factory()->make();

    foreach (listFields() as [, $property, , $attributed]) {
        if ($property->getDeclaringClass()->getName() !== $payload) {
            continue;
        }

        $list = $built->{$property->getName()} ?? [];

        expect($list)->not->toBeEmpty($payload.'::$'.$property->getName().' built nothing to check')
            ->and($list[array_key_first($list)])->toBeInstanceOf($attributed);
    }
})->with(array_values(array_unique(array_map(
    fn (array $field): string => $field[1]->getDeclaringClass()->getName(),
    listFields(),
))));
