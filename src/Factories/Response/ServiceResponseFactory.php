<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use Routegroup\IngPay\Factories\ResponseFactory;
use Routegroup\IngPay\Response\ServiceResponse;
use Routegroup\IngPay\Value\ServiceData;

/** @extends ResponseFactory<ServiceResponse> */
class ServiceResponseFactory extends ResponseFactory
{
    protected string $payload = ServiceResponse::class;

    protected function definition(): array
    {
        return [
            'service' => ServiceData::factory()->make()->toArray(),
        ];
    }
}
