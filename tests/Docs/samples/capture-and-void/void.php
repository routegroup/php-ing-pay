<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;

$transactionId = '79333e36-883a-4566-a84e-97c933b37e19';

$transaction = $api->voidTransaction($transactionId)->transaction;
// #endregion sample

return $transaction;
