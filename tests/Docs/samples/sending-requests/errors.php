<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Exceptions\ApiErrorException;

try {
    $transaction = $api->getTransaction('tx-1042')->transaction;
} catch (ApiErrorException $e) {
    if ($e->status() !== 404) {
        throw $e;
    }

    $transaction = null;
}
// #endregion sample

return $transaction;
