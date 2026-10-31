<?php

declare(strict_types=1);

/**
 * @var Api $api
 * @var PaymentProfile $card
 */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\ChargeProfileRequest;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Value\PaymentProfile;

$transaction = $api->chargeProfile(new ChargeProfileRequest([
    'paymentProfileId' => $card->id,
    'amount' => 2999,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1043',
    'title' => 'Order 1043',
]))->transaction;
// #endregion sample

return $transaction;
