<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Value\PaymentProfile;

$profiles = $api->getProfilesByCid('customer-1042')->paymentProfiles ?? [];

$cards = array_map(
    fn (PaymentProfile $profile): string => "{$profile->organization} {$profile->maskedNumber}, {$profile->month}/{$profile->year}",
    array_filter($profiles, fn (PaymentProfile $profile): bool => $profile->isActive === true),
);
// #endregion sample

return $cards;
