<?php

declare(strict_types=1);

/**
 * @var Paywall $paywall
 * @var CreateTransactionRequest $request
 */

// #region sample
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;
use Routegroup\IngPay\Types\Lang;

$url = $paywall->url($request, Lang::EN);
// #endregion sample

return $url;
