<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\InstallmentsCalculatorRequest;

$paywall = new Paywall($config, new Utils($config));

$fields = $paywall->fields(new InstallmentsCalculatorRequest(['amount' => 129900]));

echo '<div id="'.InstallmentsCalculatorRequest::WRAPPER_ID.'"></div>'
    .'<script src="'.$config->env->installmentsUrl().'" id="'.InstallmentsCalculatorRequest::SCRIPT_ID.'"></script>'
    .'<script>document.getElementById("'.InstallmentsCalculatorRequest::WRAPPER_ID.'")'
    .'.imojeInstallments('.json_encode($fields).');</script>';
// #endregion sample

return $fields;
