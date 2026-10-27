<?php

declare(strict_types=1);

namespace Routegroup\IngPay;

/**
 * A request the gateway signs only part of. The card widget is one: it adds
 * presentation fields after the signature is computed, so signing them would
 * produce a digest the gateway cannot reproduce.
 */
interface HasUnsignedFields
{
    /** @return list<string> */
    public function unsignedFields(): array;
}
