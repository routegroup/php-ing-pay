<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use Routegroup\IngPay\Factories\ResponseFactory;
use Routegroup\IngPay\Response\ProfileResponse;
use Routegroup\IngPay\Value\PaymentProfile;

/** @extends ResponseFactory<ProfileResponse> */
class ProfileResponseFactory extends ResponseFactory
{
    protected string $payload = ProfileResponse::class;

    protected function definition(): array
    {
        return [
            'paymentProfile' => PaymentProfile::factory(),
        ];
    }
}
