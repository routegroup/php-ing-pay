<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Value;

use Routegroup\IngPay\Value\TransactionPayment;

/**
 * A payment object from a notification the sandbox actually sent, rather than
 * one assembled from the specification. Two of its fields were being dropped
 * because nothing had ever looked at a real one.
 *
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function sentPayment(array $overrides = []): array
{
    return array_merge([
        'id' => '79333e36-883a-4566-a84e-97c933b37e19',
        'title' => '',
        'amount' => 1000,
        'status' => 'settled',
        'created' => 1793090952,
        'orderId' => 'HOOK-fead3f98',
        'currency' => 'PLN',
        'modified' => 1793091008,
        'serviceId' => '8b65743a-11c8-4462-9fd2-d77328271191',
        'ipksef' => '',
        'notificationUrl' => 'https://example.com/notify',
    ], $overrides);
}

it('keeps every field the gateway sends in a payment', function (): void {
    $sent = sentPayment();

    expect(array_diff(array_keys($sent), array_keys(new TransactionPayment($sent)->toArray())))->toBeEmpty();
});

it('reads back the two fields that were being dropped', function (): void {
    $payment = new TransactionPayment(sentPayment(['ipksef' => 'KSEF-123']));

    expect($payment->notificationUrl)->toBe('https://example.com/notify')
        ->and($payment->ipksef)->toBe('KSEF-123');
});
