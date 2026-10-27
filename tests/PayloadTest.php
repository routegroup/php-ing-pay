<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests;

use Error;
use Routegroup\IngPay\Exceptions\MissingFieldException;
use Routegroup\IngPay\Tests\Fixtures\LenientPayload;
use Routegroup\IngPay\Tests\Fixtures\RequiredPayload;
use Routegroup\IngPay\Tests\Fixtures\StrictEnumPayload;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Fee;
use RuntimeException;
use ValueError;

it('converts a wire value to the declared type', function (): void {
    $payload = new LenientPayload(['count' => '42', 'flag' => 1, 'label' => 7, 'rate' => '1.5']);

    expect($payload->count)->toBe(42)
        ->and($payload->flag)->toBeTrue()
        ->and($payload->label)->toBe('7')
        ->and($payload->rate)->toBe(1.5);
});

it('hydrates an enum from its backing value', function (): void {
    expect(new LenientPayload(['status' => 'settled'])->status)->toBe(TransactionStatus::SETTLED);
});

it('leaves an enum alone when it already arrived as one', function (): void {
    expect(new LenientPayload(['status' => TransactionStatus::REFUND])->status)->toBe(TransactionStatus::REFUND);
});

it('hydrates an unknown enum value to null', function (): void {
    expect(new LenientPayload(['status' => 'teleported'])->status)->toBeNull();
});

it('raises on an unknown enum value when the payload is strict', function (): void {
    expect(fn (): StrictEnumPayload => new StrictEnumPayload(['status' => 'teleported']))->toThrow(ValueError::class);
});

it('hydrates a nested payload from a nested array', function (): void {
    $fee = new LenientPayload(['fee' => ['merchant' => '120', 'surcharge' => null]])->fee;

    expect($fee)->toBeInstanceOf(Fee::class)
        ->and($fee->merchant)->toBe(120)
        ->and($fee->surcharge)->toBeNull();
});

it('leaves a nested payload alone when it already arrived built', function (): void {
    $fee = new Fee(['merchant' => 5]);

    expect(new LenientPayload(['fee' => $fee])->fee)->toBe($fee);
});

it('fills an absent optional field with null', function (): void {
    expect(new LenientPayload([])->count)->toBeNull();
});

it('names the payload, the field and the type when a required field is absent', function (): void {
    expect(fn (): RequiredPayload => new RequiredPayload([]))
        ->toThrow(MissingFieldException::class, 'requires [id] of type string');
});

it('carries the missing field for logging', function (): void {
    try {
        new RequiredPayload([]);
    } catch (MissingFieldException $e) {
        expect($e->field)->toBe('id')
            ->and($e->expected)->toBe('string')
            ->and($e->context())->toHaveKeys(['payload', 'field', 'expected']);

        return;
    }

    throw new RuntimeException('MissingFieldException was not thrown.');
});

it('refuses to be modified after hydration', function (): void {
    $payload = new LenientPayload(['count' => 1]);

    // @phpstan-ignore assign.propertyProtectedSet (refusing this at runtime is what the test asserts)
    expect(fn (): int => $payload->count = 2)->toThrow(Error::class, 'Cannot modify readonly property');
});

it('drops nulls, unwraps enums and flattens nested payloads in toArray', function (): void {
    $payload = new LenientPayload([
        'count' => 3,
        'status' => 'settled',
        'fee' => ['merchant' => 10],
    ]);

    expect($payload->toArray())->toBe([
        'count' => 3,
        'status' => 'settled',
        'fee' => ['merchant' => 10],
    ]);
});

it('ignores keys it does not declare rather than failing on them', function (): void {
    $payload = new LenientPayload(['count' => 1, 'somethingIngAddedLastTuesday' => 'x']);

    expect($payload->toArray())->toBe(['count' => 1]);
});
