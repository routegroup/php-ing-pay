<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Docs;

use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\CanRefundResponse;
use Routegroup\IngPay\Response\RefundResponse;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Types\TransactionType;
use Routegroup\IngPay\Value\Transaction;

/** The samples on the Refunds page, in the order the page shows them. */
it('checks a refund can be made, then makes it', function (): void {
    $client = new FakeClient()->queue(CanRefundResponse::factory()->make()->toArray())
        ->queue(RefundResponse::factory()->make([
            'transaction' => Transaction::factory()->make([
                'id' => 'refund-1',
                'type' => TransactionType::REFUND,
                'status' => TransactionStatus::NEW,
            ]),
        ])->toArray());
    $api = docsApi($client);

    $check = require __DIR__.'/samples/refunds/can-refund.php';

    expect($check->refundable)->toBeTrue()
        ->and($client->lastRequest()?->url)->toEndWith('/transaction/79333e36-883a-4566-a84e-97c933b37e19/can-refund')
        ->and($client->lastRequest()?->body)->toMatchArray(['amount' => 2000]);

    $refund = require __DIR__.'/samples/refunds/refund.php';

    expect($client->lastRequest()?->url)->toEndWith('/transaction/79333e36-883a-4566-a84e-97c933b37e19/refund')
        ->and($client->lastRequest()?->body)->toMatchArray(['amount' => 2000, 'type' => 'refund', 'serviceId' => 'service-1'])
        ->and($refund->type)->toBe(TransactionType::REFUND)
        ->and($refund->id)->toBe('refund-1');
});

it('tests a refund against a fake client', function (): void {
    $config = docsConfig();

    $client = require __DIR__.'/samples/refunds/testing.php';

    expect($client->sentCount())->toBe(1);
});
