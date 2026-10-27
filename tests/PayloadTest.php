<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests;

use Routegroup\IngPay\Exceptions\MissingFieldException;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Fee;

function lenient(array $data): Payload
{
    return new class($data) extends Payload
    {
        public readonly ?int $count;

        public readonly ?bool $flag;

        public readonly ?string $label;

        public readonly ?float $rate;

        public readonly ?TransactionStatus $status;

        public readonly ?Fee $fee;
    };
}

function required(array $data): Payload
{
    return new class($data) extends Payload
    {
        public readonly string $id;
    };
}

function strict(array $data): Payload
{
    return new class($data) extends Payload
    {
        protected bool $strictEnums = true;

        public readonly ?TransactionStatus $status;
    };
}

it('converts a wire value to the declared type', function (): void {
    $payload = lenient(['count' => '42', 'flag' => 1, 'label' => 7, 'rate' => '1.5']);

    expect($payload->count)->toBe(42)
        ->and($payload->flag)->toBeTrue()
        ->and($payload->label)->toBe('7')
        ->and($payload->rate)->toBe(1.5);
});

it('hydrates an enum from its backing value', function (): void {
    expect(lenient(['status' => 'settled'])->status)->toBe(TransactionStatus::SETTLED);
});

it('leaves an enum alone when it already arrived as one', function (): void {
    expect(lenient(['status' => TransactionStatus::REFUND])->status)->toBe(TransactionStatus::REFUND);
});

it('hydrates an unknown enum value to null', function (): void {
    expect(lenient(['status' => 'teleported'])->status)->toBeNull();
});

it('raises on an unknown enum value when the payload is strict', function (): void {
    expect(fn () => strict(['status' => 'teleported']))->toThrow(\ValueError::class);
});

it('hydrates a nested payload from a nested array', function (): void {
    $fee = lenient(['fee' => ['merchant' => '120', 'surcharge' => null]])->fee;

    expect($fee)->toBeInstanceOf(Fee::class)
        ->and($fee->merchant)->toBe(120)
        ->and($fee->surcharge)->toBeNull();
});

it('leaves a nested payload alone when it already arrived built', function (): void {
    $fee = new Fee(['merchant' => 5]);

    expect(lenient(['fee' => $fee])->fee)->toBe($fee);
});

it('fills an absent optional field with null', function (): void {
    expect(lenient([])->count)->toBeNull();
});

it('names the payload, the field and the type when a required field is absent', function (): void {
    expect(fn () => required([]))
        ->toThrow(MissingFieldException::class, 'requires [id] of type string');
});

it('carries the missing field for logging', function (): void {
    try {
        required([]);
    } catch (MissingFieldException $e) {
        expect($e->field)->toBe('id')
            ->and($e->expected)->toBe('string')
            ->and($e->context())->toHaveKeys(['payload', 'field', 'expected']);

        return;
    }

    throw new \RuntimeException('MissingFieldException was not thrown.');
});

it('refuses to be modified after hydration', function (): void {
    $payload = lenient(['count' => 1]);

    expect(fn () => $payload->count = 2)->toThrow(\Error::class, 'Cannot modify readonly property');
});

it('drops nulls, unwraps enums and flattens nested payloads in toArray', function (): void {
    $payload = lenient([
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
    $payload = lenient(['count' => 1, 'somethingIngAddedLastTuesday' => 'x']);

    expect($payload->toArray())->toBe(['count' => 1]);
});
