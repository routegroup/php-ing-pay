<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Http;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response as PsrResponse;
use Psr\Http\Message\ResponseInterface;
use Routegroup\IngPay\Http\Psr18Client;
use Routegroup\IngPay\Http\Request;
use Routegroup\IngPay\Tests\Http\Fixtures\RecordingPsrClient;

/** @return array{Psr18Client, RecordingPsrClient} */
function adapter(ResponseInterface $answer): array
{
    $psr = new RecordingPsrClient($answer);
    $factory = new Psr17Factory;

    return [new Psr18Client($psr, $factory, $factory), $psr];
}

it('carries the method and url through to the PSR request', function (): void {
    [$client, $psr] = adapter(new PsrResponse(200, [], '{}'));

    $client->send(Request::get('https://api.example/v1/transaction'));

    expect($psr->seen?->getMethod())->toBe('GET')
        ->and((string) $psr->seen?->getUri())->toBe('https://api.example/v1/transaction');
});

it('encodes a body as JSON and says so', function (): void {
    [$client, $psr] = adapter(new PsrResponse(201, [], '{}'));

    $client->send(Request::post('https://api.example/v1/transaction', ['amount' => 1000, 'title' => 'Zażółć']));

    expect((string) $psr->seen?->getBody())->toBe('{"amount":1000,"title":"Zażółć"}')
        ->and($psr->seen?->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and($psr->seen?->getHeaderLine('Accept'))->toBe('application/json');
});

it('sends no content type when there is no body', function (): void {
    [$client, $psr] = adapter(new PsrResponse(200, [], '{}'));

    $client->send(Request::get('https://api.example/v1/services'));

    expect($psr->seen?->hasHeader('Content-Type'))->toBeFalse()
        ->and((string) $psr->seen?->getBody())->toBeEmpty();
});

it('passes our own headers along', function (): void {
    [$client, $psr] = adapter(new PsrResponse(200, [], '{}'));

    $client->send(Request::get('https://api.example/v1/services')->withHeaders(['Authorization' => 'Bearer secret']));

    expect($psr->seen?->getHeaderLine('Authorization'))->toBe('Bearer secret');
});

it('brings the status, body and headers back', function (): void {
    [$client] = adapter(new PsrResponse(422, ['Content-Type' => 'application/json'], '{"status":"error"}'));

    $answer = $client->send(Request::get('https://api.example/v1/transaction'));

    expect($answer->status)->toBe(422)
        ->and($answer->failed())->toBeTrue()
        ->and($answer->json())->toBe(['status' => 'error'])
        ->and($answer->header('content-type'))->toBe('application/json');
});
