<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs;

use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\ChargeProfileResponse;
use Routegroup\IngPay\Response\ProfileResponse;
use Routegroup\IngPay\Response\ProfilesResponse;
use Routegroup\IngPay\Value\PaymentProfile;

/** The samples on the Payment Profiles page, in the order the page shows them. */
it('lists the active cards a customer saved', function (): void {
    $client = FakeClient::answering(ProfilesResponse::factory()->make([
        'paymentProfiles' => [
            PaymentProfile::factory()->make(['organization' => 'VISA', 'maskedNumber' => '****1111', 'month' => 12, 'year' => 2029, 'isActive' => true]),
            PaymentProfile::factory()->make(['isActive' => false]),
        ],
    ])->toArray());
    $api = docsApi($client);

    $cards = require __DIR__.'/samples/payment-profiles/list.php';

    expect($client->lastRequest()?->url)->toEndWith('/profile/cid/customer-1042')
        ->and($cards)->toBe(['VISA ****1111, 12/2029']);
});

it('charges a profile, then deactivates it', function (): void {
    $client = new FakeClient()->queue(ChargeProfileResponse::factory()->make()->toArray())
        ->queue(ProfileResponse::factory()->make()->toArray())
        ->queue(ProfileResponse::factory()->make([
            'paymentProfile' => PaymentProfile::factory()->make(['isActive' => false]),
        ])->toArray());
    $api = docsApi($client);

    require __DIR__.'/samples/payment-profiles/charge.php';

    expect($client->lastRequest()?->url)->toEndWith('/transaction/profile')
        ->and($client->lastRequest()?->body)->toMatchArray([
            'paymentProfileId' => '2da19cca-3a30-48fe-abb1-a5df9ea88f01',
            'amount' => 4999,
            'serviceId' => 'service-1',
        ]);

    $profile = require __DIR__.'/samples/payment-profiles/deactivate.php';

    expect($client->sent()[1]->method)->toBe('DELETE')
        ->and($profile?->isActive)->toBeFalse();
});

it('tests a charge against a fake client', function (): void {
    $config = docsConfig();

    $client = require __DIR__.'/samples/payment-profiles/testing.php';

    expect($client->sentCount())->toBe(1);
});
