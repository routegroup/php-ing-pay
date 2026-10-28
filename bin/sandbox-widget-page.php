<?php

/**
 * Writes the signed widget page and names the customer id to look up by.
 *
 *   php bin/sandbox-widget-page.php
 */
declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Paywall\OneClickRequest;
use Routegroup\IngPay\Tests\Sandbox\Sandbox;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\HashMethod;
use Routegroup\IngPay\Types\WidgetType;

$missing = Sandbox::missing();

if ($missing !== []) {
    fwrite(STDERR, 'Sandbox credentials are not set: '.implode(', ', $missing).PHP_EOL);

    exit(78);
}

$config = Sandbox::config();
$cid = 'cid-'.bin2hex(random_bytes(4));
$base = 'http://127.0.0.1:'.Sandbox::PORT;

$request = new OneClickRequest([
    'widgetType' => WidgetType::ONECLICK,
    'amount' => 1000,
    'currency' => Currency::PLN,
    'orderId' => Sandbox::orderId('CI-WIDGET'),
    'customerId' => $cid,
    'customerFirstName' => 'Anna',
    'customerLastName' => 'Kowalska',
    'customerEmail' => 'anna@example.com',
    'orderDescription' => 'Card profile registration',
    'urlSuccess' => $base.'/done?r=success',
    'urlFailure' => $base.'/done?r=failure',
    'urlReturn' => $base.'/done?r=return',
    'locale' => 'pl',
    'inline' => true,
])->withIdentity($config);

$fields = $request->toArray();
unset($fields['signature']);

$signed = array_diff_key($fields, array_flip($request->unsignedFields()));
$fields['signature'] = new Utils($config)->createSignature($signed, HashMethod::SHA256);

$attributes = [];

foreach ($fields as $name => $value) {
    // toArray() renders a bool as 1; a data-* attribute wants true or false.
    $declared = property_exists($request, $name) ? $request->$name : null;

    $attributes[] = sprintf(
        '  data-%s="%s"',
        strtolower((string) preg_replace('/(?<!^)[A-Z]/', '-$0', $name)),
        htmlspecialchars(is_bool($declared) ? ($declared ? 'true' : 'false') : (string) $value, ENT_QUOTES),
    );
}

$page = '<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>Card profile registration</title>
<style>body{font:16px/1.5 system-ui,sans-serif;margin:0;padding:24px}
#imoje-widget__wrapper{min-height:520px}</style>
</head>
<body>
<div id="imoje-widget__wrapper"></div>
<script
  src="'.htmlspecialchars($config->env->widgetUrl()).'"
  id="'.OneClickRequest::SCRIPT_ID.'"
'.implode("\n", $attributes).'
></script>
</body>
</html>
';

@mkdir(Sandbox::root().'/.sandbox', 0o775, true);
file_put_contents(Sandbox::root().'/.sandbox/widget.html', $page);

$githubEnv = getenv('GITHUB_ENV');

if (is_string($githubEnv) && $githubEnv !== '') {
    file_put_contents($githubEnv, 'SANDBOX_CID='.$cid."\n", FILE_APPEND);
}

echo 'cid  : ', $cid, PHP_EOL;
echo 'page : ', $base, '/widget', PHP_EOL;
