<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;

$trusted = $api->getTrustedIps()->trustedIps ?? [];
// #endregion sample

return $trusted;
