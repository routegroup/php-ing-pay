<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Concerns;

use Routegroup\IngPay\Lib\Config;

/**
 * A request says what to do, not who is asking. The merchant and service the
 * gateway identifies the caller by are filled in on the way out, so a request
 * can be built, compared and stored with no configuration in scope.
 *
 * Keys a request does not declare are ignored, so one method serves both the
 * API, which identifies by service, and the paywall, which also needs the
 * merchant.
 */
trait CarriesIdentity
{
    public function withIdentity(Config $config): static
    {
        return new static([
            'merchantId' => $config->merchantId,
            'serviceId' => $config->serviceId,
            ...$this->toArray(),
        ]);
    }
}
