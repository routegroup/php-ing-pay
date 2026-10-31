<?php

declare(strict_types=1);

/**
 * @var Api $api
 * @var Config $config
 */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\GetPaymentMethodsRequest;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Types\Currency;

$groups = $api->getPaymentMethods(
    new GetPaymentMethodsRequest(['amount' => 4999, 'currency' => Currency::PLN]),
    $config->serviceId,
)->methods ?? [];

$picker = [];

foreach ($groups as $group) {
    foreach ($group->channels as $channel) {
        $picker[$group->label][] = [
            'label' => $channel->label,
            'paymentMethod' => $channel->paymentMethod,
            'paymentMethodCode' => $channel->paymentMethodCode,
        ];
    }
}
// #endregion sample

return $picker;
