<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CanRefundRequest;

$transactionId = '79333e36-883a-4566-a84e-97c933b37e19';

$check = $api->canRefund(new CanRefundRequest(['amount' => 2000]), $transactionId);

if ($check->refundable === true) {
    // 2000 can go back; a partial refund lies between
    // $check->partialRefund?->minRefundAmount and ->maxRefundAmount
}
// #endregion sample

return $check;
