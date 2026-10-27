<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use Routegroup\IngPay\Factories\ResponseFactory;
use Routegroup\IngPay\Response\ServicesResponse;

/** @extends ResponseFactory<ServicesResponse> */
class ServicesResponseFactory extends ResponseFactory
{
    protected string $payload = ServicesResponse::class;

    /** The gateway answers with a top-level list of {service} objects. */
    protected function definition(): array
    {
        return [
            [
                'service' => [
                    'id' => $this->faker()->uuid(),
                    'created' => $this->faker()->unixTime(),
                    'isActive' => true,
                    'name' => $this->faker()->company(),
                    'paymentMethods' => [
                        ['paymentMethod' => 'pbl', 'paymentMethodCode' => 'ipko'],
                    ],
                ],
            ],
        ];
    }
}
