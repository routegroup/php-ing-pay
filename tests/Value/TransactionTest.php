<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Value;

use Error;
use Routegroup\IngPay\Exceptions\MissingFieldException;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Types\TransactionSource;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Types\TransactionType;
use Routegroup\IngPay\Value\Action;
use Routegroup\IngPay\Value\Fee;
use Routegroup\IngPay\Value\PayerCardData;
use Routegroup\IngPay\Value\PaymentSummary;
use Routegroup\IngPay\Value\Transaction;

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function wire(array $overrides = []): array
{
    return array_replace([
        'id' => 'f1d2c3b4-0000-4000-8000-abcdefabcdef',
        'type' => 'sale',
        'status' => 'settled',
        'source' => 'web',
        'created' => 1761500000,
        'modified' => 1761500120,
        'serviceId' => 'service-1',
        'amount' => 12900,
        'currency' => 'PLN',
        'orderId' => 'ORDER-2026-0001',
    ], $overrides);
}

it('hydrates the fields a settled card transaction carries', function (): void {
    $transaction = new Transaction(wire([
        'paymentMethod' => 'card',
        'paymentMethodCode' => 'blik',
        'title' => 'Order 1',
        'paidAmount' => 12900,
        'fee' => ['merchant' => 199, 'surcharge' => 0],
        'payment' => ['id' => 'payment-1', 'status' => 'settled'],
        'payerCardData' => ['brand' => 'visa', 'last4' => '4242'],
        'action' => ['type' => 'redirect', 'url' => 'https://example.com/back'],
    ]));

    expect($transaction->id)->toBe('f1d2c3b4-0000-4000-8000-abcdefabcdef')
        ->and($transaction->type)->toBe(TransactionType::SALE)
        ->and($transaction->status)->toBe(TransactionStatus::SETTLED)
        ->and($transaction->source)->toBe(TransactionSource::WEB)
        ->and($transaction->currency)->toBe(Currency::PLN)
        ->and($transaction->paymentMethod)->toBe(PaymentMethod::CARD)
        ->and($transaction->paymentMethodCode)->toBe(PaymentMethodCode::BLIK)
        ->and($transaction->amount)->toBe(12900)
        ->and($transaction->created)->toBe(1761500000)
        ->and($transaction->fee)->toBeInstanceOf(Fee::class)
        ->and($transaction->fee->merchant)->toBe(199)
        ->and($transaction->payment)->toBeInstanceOf(PaymentSummary::class)
        ->and($transaction->payment->status)->toBe(TransactionStatus::SETTLED)
        ->and($transaction->payerCardData)->toBeInstanceOf(PayerCardData::class)
        ->and($transaction->payerCardData->last4)->toBe('4242')
        ->and($transaction->action)->toBeInstanceOf(Action::class);
});

it('leaves every optional field null when the gateway sends only the essentials', function (): void {
    $transaction = new Transaction(wire());

    expect($transaction->title)->toBeNull()
        ->and($transaction->fee)->toBeNull()
        ->and($transaction->payment)->toBeNull()
        ->and($transaction->paymentProfile)->toBeNull()
        ->and($transaction->payerData)->toBeNull()
        ->and($transaction->payerCardData)->toBeNull()
        ->and($transaction->action)->toBeNull()
        ->and($transaction->refunds)->toBeNull()
        ->and($transaction->paidAmount)->toBeNull()
        ->and($transaction->notificationUrl)->toBeNull();
});

it('coerces the amounts the gateway sends as strings', function (): void {
    $transaction = new Transaction(wire(['amount' => '12900', 'created' => '1761500000']));

    expect($transaction->amount)->toBe(12900)
        ->and($transaction->created)->toBe(1761500000);
});

it('hydrates a status it does not know to null rather than failing', function (): void {
    expect((new Transaction(wire(['status' => 'reticulating_splines'])))->status)->toBeNull();
});

it('names the field when the gateway omits one the type guarantees', function (string $field): void {
    $data = wire();
    unset($data[$field]);

    expect(fn (): Transaction => new Transaction($data))
        ->toThrow(MissingFieldException::class, "requires [$field]");
})->with(['id', 'created', 'modified', 'serviceId', 'amount', 'orderId']);

/**
 * @param  array<array-key, mixed>  $data
 * @return array<array-key, mixed>
 */
function sorted(array $data): array
{
    ksort($data);

    return array_map(fn (mixed $v): mixed => is_array($v) ? sorted($v) : $v, $data);
}

it('round-trips to the shape it was given', function (): void {
    $given = wire([
        'title' => 'Order 1',
        'fee' => ['merchant' => 199],
        'payment' => ['id' => 'payment-1', 'status' => 'settled'],
    ]);

    expect(sorted(new Transaction($given)->toArray()))->toBe(sorted($given));
});

it('emits fields in declaration order, whatever order they arrived in', function (): void {
    $shuffled = array_reverse(wire(), preserve_keys: true);

    expect(array_keys(new Transaction($shuffled)->toArray()))
        ->toBe(['id', 'type', 'status', 'source', 'created', 'modified', 'serviceId', 'amount', 'currency', 'orderId']);
});

it('refuses to be modified after hydration', function (): void {
    $transaction = new Transaction(wire());

    // @phpstan-ignore assign.propertyProtectedSet (refusing this at runtime is what the test asserts)
    expect(fn (): int => $transaction->amount = 1)
        ->toThrow(Error::class, 'Cannot modify readonly property');
});
