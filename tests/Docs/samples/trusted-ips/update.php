<?php

declare(strict_types=1);

/**
 * @var Api $api
 * @var list<string> $trusted
 */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\PutTrustedIpsRequest;

$response = $api->putTrustedIps(new PutTrustedIpsRequest([
    'trustedIps' => array_values(array_unique([...$trusted, '203.0.113.10'])),
]));

$saved = $response->status === 'success';
// #endregion sample

return $saved;
