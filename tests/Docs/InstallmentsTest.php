<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs\Installments;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Types\Environment;

/** The samples on the Installments page, run in the order the page shows them. */
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

it('renders the calculator with signed fields', function (): void {
    $config = config();

    ob_start();
    $fields = require __DIR__.'/samples/installments/calculator.php';
    $html = (string) ob_get_clean();

    expect($fields)->toMatchArray(['merchantId' => 'merchant-1', 'serviceId' => 'service-1', 'amount' => 129900, 'currency' => 'PLN'])
        ->toHaveKey('signature')
        ->and($html)
        ->toContain('<div id="imoje-installments__wrapper"></div>')
        ->toContain('src="https://paywall.sandbox.pay.ing.pl/js/installments.js" id="imoje-installments__script"')
        ->toContain('.imojeInstallments('.json_encode($fields).')');
});

it('creates an instalment transaction', function (): void {
    $client = FakeClient::answering(TransactionResponse::factory()->make()->toArray());
    $api = new Api($client, config(), new Url(config()));

    require __DIR__.'/samples/installments/create.php';

    expect($client->lastRequest()?->body)->toMatchArray([
        'paymentMethod' => 'imoje_installments',
        'paymentMethodCode' => 'inbank',
        'installment' => ['period' => 12],
    ]);
});

it('refuses a period the channel does not offer', function (): void {
    $error = require __DIR__.'/samples/installments/period.php';

    expect($error)->toBe('installment.period: must be at most 10 for inbank_0');
});

it('tests against a fake client', function (): void {
    $config = config();

    $client = require __DIR__.'/samples/installments/testing.php';

    expect($client->sentCount())->toBe(1);
});
