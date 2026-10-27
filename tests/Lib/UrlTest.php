<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Lib;

use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Types\Environment;
use Routegroup\IngPay\Types\Lang;

function url(): Url
{
    return new Url(new Config(
        merchantId: '$merchant_id$',
        serviceId: '$service_id$',
        serviceKey: '$service_key$',
        apiKey: '$api_key$',
        env: Environment::SANDBOX,
    ));
}

$base = 'https://api.sandbox.pay.ing.pl/v1/merchant/$merchant_id$';

it('creates transaction url')
    ->expect(fn () => url()->createTransactionUrl())
    ->toEqual($base.'/transaction');

it('creates get transaction url')
    ->expect(fn () => url()->createGetTransactionUrl('$transaction_id$'))
    ->toEqual($base.'/transaction/$transaction_id$');

it('creates payment url')
    ->expect(fn () => url()->createPaymentUrl())
    ->toEqual($base.'/payment');

it('creates get payment url')
    ->expect(fn () => url()->createGetPaymentUrl('$payment_id$'))
    ->toEqual($base.'/payment/$payment_id$');

it('creates cancel payment url')
    ->expect(fn () => url()->createCancelPaymentUrl())
    ->toEqual($base.'/payment/cancel');

it('creates refund url')
    ->expect(fn () => url()->createRefundUrl('$transaction_id$'))
    ->toEqual($base.'/transaction/$transaction_id$/refund');

it('creates charge profile url')
    ->expect(fn () => url()->createChargeProfileUrl())
    ->toEqual($base.'/transaction/profile');

it('creates profile id url')
    ->expect(fn () => url()->createProfileIdUrl('$profile_id$'))
    ->toEqual($base.'/profile/id/$profile_id$');

it('creates capture url')
    ->expect(fn () => url()->createCaptureUrl('$transaction_id$'))
    ->toEqual($base.'/transaction/$transaction_id$/capture');

it('creates void url')
    ->expect(fn () => url()->createVoidUrl('$transaction_id$'))
    ->toEqual($base.'/transaction/$transaction_id$/void');

it('creates can refund url')
    ->expect(fn () => url()->createCanRefundUrl('$transaction_id$'))
    ->toEqual($base.'/transaction/$transaction_id$/can-refund');

it('creates profile cid url')
    ->expect(fn () => url()->createProfileCidUrl('$cid$'))
    ->toEqual($base.'/profile/cid/$cid$');

it('creates profile deactivate url')
    ->expect(fn () => url()->createProfileDeactivateUrl())
    ->toEqual($base.'/profile/deactivate');

it('creates service url')
    ->expect(fn () => url()->createServiceUrl('$service_id$'))
    ->toEqual($base.'/service/$service_id$');

it('creates services url')
    ->expect(fn () => url()->createServicesUrl())
    ->toEqual($base.'/services');

it('creates get payment methods url')
    ->expect(fn () => url()->createGetPaymentMethodsUrl('$service_id$'))
    ->toEqual($base.'/service/$service_id$/get-payment-methods');

it('creates settings ips url')
    ->expect(fn () => url()->createSettingsIpsUrl())
    ->toEqual($base.'/settings/ips');

it('creates paywall url without a language')
    ->expect(fn () => Environment::SANDBOX->paywallUrl())
    ->toEqual('https://paywall.sandbox.pay.ing.pl/payment');

it('creates paywall url for a language', function (Lang $lang, string $segment): void {
    expect(Environment::SANDBOX->paywallUrl($lang))
        ->toEqual("https://paywall.sandbox.pay.ing.pl/$segment/payment");
})->with([
    [Lang::PL, 'pl'],
    [Lang::UK, 'uk'],
]);

it('creates widget and cdn urls')
    ->expect(fn () => [Environment::SANDBOX->widgetUrl(), Environment::SANDBOX->cdnUrl()])
    ->toEqual([
        'https://paywall.sandbox.pay.ing.pl/js/widget.min.js',
        'https://cdn.pay.ing.pl',
    ]);
