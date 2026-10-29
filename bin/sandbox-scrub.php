<?php

/**
 * Rewrites what the catcher caught as fixtures safe to publish.
 *
 *   php bin/sandbox-scrub.php [directory]
 */
declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Routegroup\IngPay\Tests\Sandbox\Fixture;
use Routegroup\IngPay\Tests\Sandbox\Sandbox;

$missing = Sandbox::missing();

if ($missing !== []) {
    fwrite(STDERR, 'Sandbox credentials are not set: '.implode(', ', $missing).PHP_EOL);

    exit(78);
}

$into = $argv[1] ?? 'sandbox-notifications';
$written = Fixture::writeAll($into);

foreach ($written as $name) {
    echo $into, '/', $name, '.json', PHP_EOL;
}

echo count($written), ' scrubbed.', PHP_EOL;
