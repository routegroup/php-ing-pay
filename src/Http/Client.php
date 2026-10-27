<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Http;

/**
 * The whole HTTP surface of this package: one method.
 *
 * It is deliberately not PSR-18. PSR-18 would drag PSR-7 messages and PSR-17
 * factories into a client that makes nineteen JSON calls, and it would cost
 * Laravel integrations Http::fake() in their own tests. A PSR-18 adapter ships
 * beside this, so anyone who already has such a client can still use it.
 */
interface Client
{
    public function send(Request $request): Response;
}
