<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;

$cards = $api->getProfilesByCid('customer-17')->paymentProfiles ?? [];

foreach ($cards as $card) {
    // offer "{$card->organization} {$card->maskedNumber}, valid to {$card->month}/{$card->year}"
}
// #endregion sample

return $cards;
