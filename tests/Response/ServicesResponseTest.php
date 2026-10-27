<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Tests\Response;

use Routegroup\IngPay\Http\Response as HttpResponse;
use Routegroup\IngPay\Response\ServicesResponse;

function servicesBody(int $count = 2): string
{
    $services = [];

    for ($i = 0; $i < $count; $i++) {
        $services[] = ['service' => [
            'id' => "service-$i",
            'created' => 1761500000 + $i,
            'isActive' => true,
            'paymentMethods' => [],
        ]];
    }

    return json_encode($services, JSON_THROW_ON_ERROR);
}

it('hydrates the top-level list the endpoint answers with', function (): void {
    $response = new ServicesResponse(new HttpResponse(200, servicesBody(3)));

    expect($response->services)->toHaveCount(3)
        ->and($response->services[2]->id)->toBe('service-2');
});

it('unwraps each service from its envelope', function (): void {
    $response = new ServicesResponse(new HttpResponse(200, servicesBody(2)));

    expect($response->services[0]->id)->toBe('service-0')
        ->and($response->services[0]->created)->toBe(1761500000)
        ->and($response->services[0]->isActive)->toBeTrue()
        ->and($response->services[1]->id)->toBe('service-1');
});

it('gives an empty list when the body is not one', function (): void {
    $response = new ServicesResponse(new HttpResponse(200, '{"unexpected":"shape"}'));

    expect($response->services)->toBe([]);
});

it('flattens its services back to arrays in toArray', function (): void {
    $response = new ServicesResponse(new HttpResponse(200, servicesBody(1)));

    expect($response->toArray()['services'][0])->toBeArray()
        ->and($response->toArray()['services'][0]['id'])->toBe('service-0');
});
