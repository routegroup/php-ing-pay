<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories;

use Routegroup\IngPay\Http\Response as HttpResponse;
use Routegroup\IngPay\Payload;

/**
 * @template TPayload of Payload
 *
 * @extends Factory<TPayload>
 */
abstract class ResponseFactory extends Factory
{
    protected int $status = 200;

    /** Build a response the gateway refused, to test the unhappy path. */
    public function status(int $status): static
    {
        $clone = clone $this;
        $clone->status = $status;

        return $clone;
    }

    protected function instantiate(array $values): Payload
    {
        return new $this->payload(
            new HttpResponse($this->status, json_encode($values, JSON_THROW_ON_ERROR))
        );
    }
}
