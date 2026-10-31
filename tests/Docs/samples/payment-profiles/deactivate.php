<?php

declare(strict_types=1);

/**
 * @var Api $api
 * @var string $profileId
 */

// #region sample
use Routegroup\IngPay\Api;

$api->deactivateProfile($profileId);

$profile = $api->getProfile($profileId)->paymentProfile;
// #endregion sample

return $profile;
