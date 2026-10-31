<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs\Paywall;

use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;
use Routegroup\IngPay\Types\Environment;

/**
 * The samples on the Paywall page, run in the order the page shows them. Each
 * page's test has its own namespace, so its helpers cannot meet another page's.
 */
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

/** @return array<string, string> */
function queryOf(string $url): array
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $fields);

    /** @var array<string, string> $fields */
    return $fields;
}

it('sends the payer to a signed paywall url', function (): void {
    $config = config();

    $url = require __DIR__.'/samples/paywall/redirect.php';

    expect($url)->toStartWith('https://paywall.sandbox.pay.ing.pl/payment?')
        ->and(queryOf($url))->toMatchArray([
            'merchantId' => 'merchant-1',
            'serviceId' => 'service-1',
            'orderId' => 'ORDER-1042',
            'amount' => '4999',
        ])->toHaveKey('signature');

    $url = require __DIR__.'/samples/paywall/language.php';

    expect($url)->toStartWith('https://paywall.sandbox.pay.ing.pl/en/payment?');
});

it('renders the same signed fields as a form', function (): void {
    $config = config();
    $paywall = new Paywall($config, new Utils($config));
    $request = new CreateTransactionRequest(['amount' => 4999, 'orderId' => 'ORDER-1042']);

    ob_start();
    require __DIR__.'/samples/paywall/form.php';
    $form = (string) ob_get_clean();

    expect($form)
        ->toContain('action="https://paywall.sandbox.pay.ing.pl/payment"')
        ->toContain('name="orderId" value="ORDER-1042"')
        ->toContain('name="signature" value="'.$paywall->fields($request)['signature'].'"');
});

it('tests the url without a gateway', function (): void {
    $config = config();

    require __DIR__.'/samples/paywall/testing.php';
});
