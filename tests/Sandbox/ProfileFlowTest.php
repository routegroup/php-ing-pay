<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Sandbox;

use Routegroup\IngPay\Api\CanRefundRequest;
use Routegroup\IngPay\Api\ChargeProfileRequest;
use Routegroup\IngPay\Api\DeactivateProfileRequest;
use Routegroup\IngPay\Api\RefundRequest;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Value\PaymentProfile;

/**
 * One test, not several: the suite runs in random order and every step here
 * stands on the one before it. The profile it reads was registered by the
 * browser step, which named the customer id in SANDBOX_CID.
 */
it('charges, refunds and retires a profile the widget registered', function (): void {
    $api = Sandbox::api();
    $cid = (string) Sandbox::value('SANDBOX_CID');

    $profiles = $api->getProfilesByCid($cid)->paymentProfiles ?? [];

    expect($profiles)->not->toBeEmpty()
        ->and($profiles[0])->toBeInstanceOf(PaymentProfile::class);

    $profile = $profiles[0];

    expect($profile->isActive)->toBeTrue()
        ->and($profile->merchantCustomerId)->toBe($cid)
        ->and($profile->maskedNumber)->not->toBeEmpty();

    $id = (string) $profile->id;

    expect($api->getProfile($id)->paymentProfile?->id)->toBe($id);

    $charge = $api->chargeProfile(new ChargeProfileRequest([
        'paymentProfileId' => $id,
        'amount' => 500,
        'currency' => Currency::PLN,
        'orderId' => Sandbox::orderId('CI-CHARGE'),
        'title' => 'Profile charge',
    ]))->transaction;

    expect($charge->status?->value)->toBe('settled')
        ->and($charge->amount)->toBe(500);

    $chargeId = $charge->id;

    // One notification, already settled: waiting for pending to become settled
    // would wait forever on this path.
    $settled = Notifications::waitFor(
        fn (array $payload): bool => Notifications::isFor($payload, $chargeId, 'settled'),
        'the charge to be notified',
    );

    expect($settled['payload']['transaction']['amount'] ?? null)->toBe(500)
        ->and($api->canRefund(new CanRefundRequest(['amount' => 200, 'currency' => Currency::PLN]), $chargeId)->refundable)->toBeTrue();

    $refund = $api->createRefund(new RefundRequest([
        'amount' => 200,
        'title' => 'Partial refund',
    ]), $chargeId)->transaction;

    expect($refund->type?->value)->toBe('refund')
        ->and($refund->amount)->toBe(200);

    $refundId = $refund->id;

    Notifications::waitFor(
        fn (array $payload): bool => Notifications::isFor($payload, $refundId, 'settled'),
        'the refund to settle',
    );

    // The specification calls a refund entry shapeless; the gateway sends
    // {transaction: {...}} for each one.
    $refunds = $api->getTransaction($chargeId)->transaction->refunds ?? [];

    expect($refunds)->toHaveCount(1)
        ->and($refunds[0]['transaction']['id'] ?? null)->toBe($refundId);

    // Not cleanup — the sandbox is never cleaned up. This asserts the flag moves.
    $api->deactivateProfile($id);

    expect($api->getProfile($id)->paymentProfile?->isActive)->toBeFalse();

    $again = $api->deactivateProfileByPost(new DeactivateProfileRequest(['paymentProfileId' => $id]));

    expect($again->http()->status)->toBe(200);
})->skip(
    fn (): bool => ! sandboxIsConfigured() || Sandbox::value('SANDBOX_CID') === null,
    'needs the profile the browser step registers, named in SANDBOX_CID',
);
