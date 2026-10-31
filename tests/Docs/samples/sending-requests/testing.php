<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\GetTransactionResponse;

$client = (new FakeClient)
    ->queue(GetTransactionResponse::factory()->make()->toArray())
    ->queue(status: 404);

$api = new Api($client, $config, new Url($config));

$api->getTransaction('tx-1042');

expect(fn (): GetTransactionResponse => $api->getTransaction('tx-1043'))->toThrow(ApiErrorException::class)
    ->and($client->sentCount())->toBe(2)
    ->and($client->lastRequest()?->url)->toEndWith('/transaction/tx-1043')
    ->and($client->lastRequest()?->headers)->toHaveKey('Authorization', "Bearer {$config->apiKey}");
// #endregion sample

return $client;
