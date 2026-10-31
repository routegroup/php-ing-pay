<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;

$response = $api->getServices();

if ($response->failed()) {
    $status = $response->http()->status;
    $message = $response->error()?->message;
}
// #endregion sample

return [$status ?? null, $message ?? null];
