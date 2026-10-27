<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Response;

use Routegroup\IngPay\Factories\ResponseFactory;
use Routegroup\IngPay\Response\ProfilesResponse;
use Routegroup\IngPay\Value\PaymentProfile;

/** @extends ResponseFactory<ProfilesResponse> */
class ProfilesResponseFactory extends ResponseFactory
{
    protected string $payload = ProfilesResponse::class;

    /** The shape the specification declares for GET /profile/cid/{cid}. */
    protected function definition(): array
    {
        return [
            'paymentProfiles' => [
                PaymentProfile::factory()->make()->toArray(),
            ],
        ];
    }
}
