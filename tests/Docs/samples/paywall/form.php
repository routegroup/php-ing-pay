<?php

declare(strict_types=1);

/**
 * @var Config $config
 * @var Paywall $paywall
 * @var CreateTransactionRequest $request
 */

// #region sample
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;

$inputs = '';

foreach ($paywall->fields($request) as $name => $value) {
    $inputs .= sprintf('<input type="hidden" name="%s" value="%s">', $name, htmlspecialchars((string) $value));
}

echo '<form method="post" action="'.$config->env->paywallUrl().'">'
    .$inputs
    .'<button type="submit">Pay</button>'
    .'</form>';
// #endregion sample
