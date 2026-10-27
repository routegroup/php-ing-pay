<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests;

use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Action;
use Routegroup\IngPay\Value\Fee;
use Routegroup\IngPay\Value\PayerCardData;
use Routegroup\IngPay\Value\PayerData;
use Routegroup\IngPay\Value\PaymentProfile;
use Routegroup\IngPay\Value\PaymentSummary;
use Routegroup\IngPay\Value\Transaction;

it('resolves every payload factory by convention', function (): void {
    // The convention is string manipulation at runtime, so calling it is the
    // assertion: a renamed payload or factory raises here, not in PHPStan.
    $built = [
        Action::factory()->make(),
        Fee::factory()->make(),
        PayerCardData::factory()->make(),
        PayerData::factory()->make(),
        PaymentProfile::factory()->make(),
        PaymentSummary::factory()->make(),
        Transaction::factory()->make(),
    ];

    expect($built)->toHaveCount(7);
});

it('builds values that make sense, not merely values of the right type', function (): void {
    $transaction = Transaction::factory()->make();

    expect($transaction->id)->not->toBeEmpty()
        ->and($transaction->amount)->toBeGreaterThan(0)
        ->and($transaction->modified)->toBeGreaterThanOrEqual($transaction->created)
        ->and($transaction->orderId)->toStartWith('ORDER-');
});

it('varies what it builds rather than repeating one row', function (): void {
    $ids = array_map(
        fn (Transaction $transaction): string => $transaction->id,
        Transaction::factory()->makeMany(20),
    );

    expect(array_unique($ids))->toHaveCount(20);

    $amounts = array_map(
        fn (Transaction $transaction): int => $transaction->amount,
        Transaction::factory()->makeMany(20),
    );

    expect(count(array_unique($amounts)))->toBeGreaterThan(10);
});

it('lets a state override what the definition generated', function (): void {
    $transaction = Transaction::factory()
        ->state(['amount' => 999, 'status' => TransactionStatus::REJECTED])
        ->make();

    expect($transaction->amount)->toBe(999)
        ->and($transaction->status)->toBe(TransactionStatus::REJECTED);
});

it('lets the call site override a state', function (): void {
    expect(Transaction::factory()->state(['amount' => 999])->make(['amount' => 1])->amount)->toBe(1);
});

it('leaves the factory it was derived from untouched', function (): void {
    $factory = Transaction::factory();
    $factory->state(['amount' => 5]);

    expect($factory->make(['orderId' => 'X'])->amount)->not->toBe(5);
});

it('builds a list of the requested size', function (): void {
    expect(Transaction::factory()->makeMany(3))->toHaveCount(3)
        ->and(Transaction::factory()->count(4)->makeMany())->toHaveCount(4);
});

it('cycles a sequence across the payloads it builds', function (): void {
    $statuses = array_map(
        fn (Transaction $transaction): ?TransactionStatus => $transaction->status,
        Transaction::factory()
            ->sequence(
                ['status' => TransactionStatus::NEW],
                ['status' => TransactionStatus::SETTLED],
            )
            ->makeMany(4),
    );

    expect($statuses)->toBe([
        TransactionStatus::NEW,
        TransactionStatus::SETTLED,
        TransactionStatus::NEW,
        TransactionStatus::SETTLED,
    ]);
});

it('builds the nested payloads a named state promises', function (): void {
    $transaction = Transaction::factory()->settledCard()->make();

    expect($transaction->status)->toBe(TransactionStatus::SETTLED)
        ->and($transaction->paymentMethod)->toBe(PaymentMethod::CARD)
        ->and($transaction->payerCardData)->toBeInstanceOf(PayerCardData::class)
        ->and($transaction->fee)->toBeInstanceOf(Fee::class)
        ->and($transaction->toArray()['fee'])->toBeArray();
});
