<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs\CardWidgetAndOneClick;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\ChargeProfileResponse;
use Routegroup\IngPay\Response\ProfilesResponse;
use Routegroup\IngPay\Types\Environment;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\PaymentProfile;
use Routegroup\IngPay\Value\Transaction;

/** The samples on the Card Widget & One-Click page, run in the order the page shows them. */
function config(): Config
{
    return new Config(
        merchantId: 'merchant-1',
        serviceId: 'service-1',
        serviceKey: 'key',
        apiKey: 'token-abc',
        env: Environment::SANDBOX,
    );
}

it('renders the widget with the signed fields as data attributes', function (): void {
    $config = config();

    ob_start();
    $fields = require __DIR__.'/samples/card-widget-and-one-click/widget.php';
    $html = (string) ob_get_clean();

    expect($html)
        ->toContain('<div id="imoje-widget__wrapper"></div>')
        ->toContain('src="https://paywall.sandbox.pay.ing.pl/js/widget.min.js" id="imoje-widget__script"')
        ->toContain('data-merchant-id="merchant-1"')
        ->toContain('data-customer-id="customer-17"')
        ->toContain('data-widget-type="oneclick"')
        ->toContain('data-locale="pl"')
        ->toContain('data-signature="'.$fields['signature'].'"');
});

it('charges a card the payer saved', function (): void {
    $client = new FakeClient()
        ->queue(ProfilesResponse::factory()->make([
            'paymentProfiles' => [PaymentProfile::factory()->make(['id' => 'profile-1'])],
        ])->toArray())
        ->queue(ChargeProfileResponse::factory()->make([
            'transaction' => Transaction::factory()->make(['status' => TransactionStatus::SETTLED]),
        ])->toArray());
    $api = new Api($client, config(), new Url(config()));

    $cards = require __DIR__.'/samples/card-widget-and-one-click/profiles.php';

    expect($client->lastRequest()?->url)->toEndWith('/customer-17')
        ->and($cards)->toHaveCount(1);

    $card = $cards[0];
    $transaction = require __DIR__.'/samples/card-widget-and-one-click/charge.php';

    expect($client->lastRequest()?->body)
        ->toMatchArray(['paymentProfileId' => 'profile-1', 'amount' => 2999, 'orderId' => 'ORDER-1043'])
        ->and($transaction->status)->toBe(TransactionStatus::SETTLED);
});

it('tests that presentation fields stay out of the signature', function (): void {
    $config = config();

    require __DIR__.'/samples/card-widget-and-one-click/testing.php';
});
