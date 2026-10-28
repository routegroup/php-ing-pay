<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Sandbox;

use Routegroup\IngPay\Api\CancelPaymentRequest;
use Routegroup\IngPay\Api\CanRefundRequest;
use Routegroup\IngPay\Api\CreatePaymentRequest;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Types\TransactionType;
use RuntimeException;

it('creates a transaction and reads back what it sent', function (): void {
    $api = Sandbox::api();
    $orderId = Sandbox::orderId('CI-BLIK');
    $notificationUrl = 'https://example.test/notify/'.bin2hex(random_bytes(3));

    $created = $api->createTransaction(new CreateTransactionRequest([
        'type' => TransactionType::SALE,
        'amount' => 1000,
        'currency' => Currency::PLN,
        'orderId' => $orderId,
        'paymentMethod' => PaymentMethod::BLIK,
        'paymentMethodCode' => PaymentMethodCode::BLIK,
        'successReturnUrl' => 'https://example.test/success',
        'failureReturnUrl' => 'https://example.test/failure',
        'notificationUrl' => $notificationUrl,
        'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska', 'email' => 'anna@example.test'],
    ]))->transaction;

    expect($created->orderId)->toBe($orderId)
        ->and($created->amount)->toBe(1000)
        ->and($created->status?->value)->toBe('pending');

    $read = $api->getTransaction($created->id)->transaction;

    expect($read->orderId)->toBe($orderId)
        // Stored and echoed, but delivery still goes to the address configured
        // on the service: the gateway notifies one url and only one.
        ->and($read->notificationUrl)->toBe($notificationUrl);
})->skip(fn (): bool => ! Sandbox::isConfigured(), Sandbox::NO_CREDENTIALS);

it('opens a payment link, reads it, and cancels it', function (): void {
    $api = Sandbox::api();
    $orderId = Sandbox::orderId('CI-LINK');

    $payment = $api->createPayment(new CreatePaymentRequest([
        'amount' => 1500,
        'currency' => Currency::PLN,
        'orderId' => $orderId,
        'title' => 'Payment link',
        'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska', 'email' => 'anna@example.test'],
        'successReturnUrl' => 'https://example.test/success',
        'failureReturnUrl' => 'https://example.test/failure',
    ]))->payment;

    expect($payment->id)->not->toBeEmpty()
        ->and($payment->orderId)->toBe($orderId);

    // A read answers flat, unlike the create that wraps the payment.
    $read = $api->getPayment($payment->id);

    expect($read->id)->toBe($payment->id)
        ->and($read->status?->value)->toBe('new');

    $api->cancelPayment(new CancelPaymentRequest(['paymentId' => $payment->id]));

    $cancelled = $api->getPayment($payment->id);

    // isActive stays true after a cancel; the status is what moves.
    expect($cancelled->status?->value)->toBe('cancelled')
        ->and($cancelled->isActive)->toBeTrue();
})->skip(fn (): bool => ! Sandbox::isConfigured(), Sandbox::NO_CREDENTIALS);

it('will not refund a transaction nobody paid', function (): void {
    $api = Sandbox::api();

    $created = $api->createTransaction(new CreateTransactionRequest([
        'type' => TransactionType::SALE,
        'amount' => 1000,
        'currency' => Currency::PLN,
        'orderId' => Sandbox::orderId('CI-UNPAID'),
        'paymentMethod' => PaymentMethod::BLIK,
        'paymentMethodCode' => PaymentMethodCode::BLIK,
        'successReturnUrl' => 'https://example.test/success',
        'failureReturnUrl' => 'https://example.test/failure',
        'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska', 'email' => 'anna@example.test'],
    ]))->transaction;

    // The gateway raises rather than answering refundable: false.
    expect(fn (): mixed => $api->canRefund(
        new CanRefundRequest(['amount' => 1000, 'currency' => Currency::PLN]),
        $created->id,
    ))->toThrow(ApiErrorException::class);
})->skip(fn (): bool => ! Sandbox::isConfigured(), Sandbox::NO_CREDENTIALS);

it('reaches capture and void well-formed, and is told the status is wrong', function (string $method): void {
    $api = Sandbox::api();

    $created = $api->createTransaction(new CreateTransactionRequest([
        'type' => TransactionType::SALE,
        'amount' => 1000,
        'currency' => Currency::PLN,
        'orderId' => Sandbox::orderId('CI-TWOPHASE'),
        'paymentMethod' => PaymentMethod::BLIK,
        'paymentMethodCode' => PaymentMethodCode::BLIK,
        'successReturnUrl' => 'https://example.test/success',
        'failureReturnUrl' => 'https://example.test/failure',
        'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska', 'email' => 'anna@example.test'],
    ]))->transaction;

    try {
        $api->$method($created->id);
    } catch (ApiErrorException $exception) {
        // 422 means well formed but wrong state; 400 would mean the content
        // type went missing again.
        expect($exception->status())->toBe(422);

        return;
    }

    throw new RuntimeException($method.' captured a transaction nobody authorised');
})->with(['captureTransaction', 'voidTransaction'])
    ->skip(fn (): bool => ! Sandbox::isConfigured(), Sandbox::NO_CREDENTIALS);
