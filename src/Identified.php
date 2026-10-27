<?php

declare(strict_types=1);

namespace Routegroup\IngPay;

use Routegroup\IngPay\Lib\Config;

/**
 * A request that the client fills the caller's identity into on the way out.
 */
interface Identified
{
    public function withIdentity(Config $config): static;
}
