<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Http;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Routegroup\IngPay\Http\Psr18Client;
use Routegroup\IngPay\Http\Request;

function guzzleAnswering(GuzzleResponse ...$answers): Psr18Client
{
    return Psr18Client::guzzle(['handler' => HandlerStack::create(new MockHandler($answers))]);
}

it('carries a request through guzzle and brings the answer back', function (): void {
    $client = guzzleAnswering(new GuzzleResponse(201, ['Content-Type' => 'application/json'], '{"id":"abc"}'));

    $answer = $client->send(Request::post('https://api.sandbox.pay.ing.pl/v1/transaction', ['amount' => 1000]));

    expect($answer->status)->toBe(201)
        ->and($answer->successful())->toBeTrue()
        ->and($answer->json())->toBe(['id' => 'abc'])
        ->and($answer->header('content-type'))->toBe('application/json');
});

it('brings a refusal back rather than raising inside guzzle', function (): void {
    $client = guzzleAnswering(new GuzzleResponse(422, [], '{"apiErrorResponse":{"message":"nope"}}'));

    $answer = $client->send(Request::get('https://api.sandbox.pay.ing.pl/v1/services'));

    expect($answer->failed())->toBeTrue()
        ->and($answer->status)->toBe(422);
});
