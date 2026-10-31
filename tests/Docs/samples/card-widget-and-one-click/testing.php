<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\OneClickRequest;
use Routegroup\IngPay\Types\Lang;

$paywall = new Paywall($config, new Utils($config));

$request = OneClickRequest::factory()->make()->toArray();

$polish = $paywall->fields(new OneClickRequest([...$request, 'locale' => Lang::PL]));
$english = $paywall->fields(new OneClickRequest([...$request, 'locale' => Lang::EN]));

expect($polish['signature'])->toBe($english['signature']);
// #endregion sample
