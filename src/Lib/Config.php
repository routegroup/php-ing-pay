<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Lib;

use Routegroup\IngPay\Types\Environment;

class Config
{
    public function __construct(
        public readonly string $merchantId,
        public readonly string $serviceId,
        public readonly string $serviceKey,
        public readonly string $apiKey,
        public readonly Environment $env,
        public readonly bool $strictErrors = true,
        public readonly int|float $timeout = 30,
        public readonly int|float $connectTimeout = 10,
    ) {}
}
