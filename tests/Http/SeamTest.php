<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Http;

use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Http\Request;
use Routegroup\IngPay\Http\Response;
use RuntimeException;
use UnexpectedValueException;

it('builds a request for each verb the gateway uses', function (): void {
    expect(Request::get('https://api.example/x')->method)->toBe('GET')
        ->and(Request::post('https://api.example/x', ['a' => 1])->body)->toBe(['a' => 1])
        ->and(Request::put('https://api.example/x')->method)->toBe('PUT');
});

it('returns a new request when headers are added', function (): void {
    $first = Request::get('https://api.example/x');
    $second = $first->withHeaders(['Authorization' => 'Bearer t']);

    expect($first->headers)->toBeEmpty()
        ->and($second->headers)->toBe(['Authorization' => 'Bearer t'])
        ->and($second)->not->toBe($first);
});

it('reads a header whatever case it arrived in', function (): void {
    $response = new Response(200, '', ['Content-Type' => 'application/json']);

    expect($response->header('content-type'))->toBe('application/json')
        ->and($response->header('CONTENT-TYPE'))->toBe('application/json')
        ->and($response->header('X-Absent'))->toBeNull();
});

it('calls 2xx successful and everything else failed', function (int $status, bool $ok): void {
    expect(new Response($status, '')->successful())->toBe($ok)
        ->and(new Response($status, '')->failed())->toBe(! $ok);
})->with([[200, true], [201, true], [299, true], [300, false], [404, false], [422, false], [500, false]]);

it('decodes an empty body to an empty array rather than failing', function (): void {
    expect(new Response(404, '')->json())->toBeEmpty()
        ->and(new Response(404, '   ')->json())->toBeEmpty();
});

it('raises when a body is not a JSON object', function (): void {
    expect(fn (): array => new Response(502, '<html>Bad gateway</html>')->json())
        ->toThrow(UnexpectedValueException::class, 'answered 502');
});

it('answers with what it was queued, in order', function (): void {
    $client = (new FakeClient)->queue(['step' => 1])->queue(['step' => 2], 201);

    expect($client->send(Request::get('https://api.example/a'))->json())->toBe(['step' => 1])
        ->and($client->send(Request::get('https://api.example/b'))->status)->toBe(201);
});

it('answers everything the same way when told to', function (): void {
    $client = FakeClient::answering(['status' => 'ok']);

    expect($client->send(Request::get('https://api.example/a'))->json())->toBe(['status' => 'ok'])
        ->and($client->send(Request::get('https://api.example/b'))->json())->toBe(['status' => 'ok']);
});

it('keeps what it was asked, so a test can assert on it', function (): void {
    $client = FakeClient::answering();
    $client->send(Request::post('https://api.example/transaction', ['amount' => 100]));

    expect($client->sentCount())->toBe(1)
        ->and($client->lastRequest()?->url)->toBe('https://api.example/transaction')
        ->and($client->lastRequest()?->body)->toBe(['amount' => 100])
        ->and($client->sent())->toHaveCount(1);
});

it('says so rather than inventing an answer when it runs out', function (): void {
    $client = (new FakeClient)->queue(['once' => true]);
    $client->send(Request::get('https://api.example/a'));

    expect(fn (): Response => $client->send(Request::get('https://api.example/b')))
        ->toThrow(RuntimeException::class, 'nothing left to answer GET https://api.example/b');
});
