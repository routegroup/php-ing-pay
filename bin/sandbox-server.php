<?php

/**
 * Serves the widget page and catches the gateway's notifications.
 *
 *   php -S 127.0.0.1:8000 bin/sandbox-server.php
 *
 * The body is stored raw: re-encoding breaks the signature.
 */
declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Routegroup\IngPay\Tests\Sandbox\Sandbox;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET') {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $page = Sandbox::root().'/.sandbox/widget.html';

    if ($path === '/widget' && is_file($page)) {
        header('Content-Type: text/html; charset=utf-8');
        readfile($page);

        exit;
    }

    header('Content-Type: text/plain; charset=utf-8');
    echo 'ok';

    exit;
}

$headers = [];

foreach ($_SERVER as $key => $value) {
    if (str_starts_with((string) $key, 'HTTP_')) {
        $headers[strtolower(str_replace('_', '-', substr((string) $key, 5)))] = $value;
    }
}

$file = sprintf('%s/%s-%s.json', Sandbox::caught(), date('Ymd-His'), bin2hex(random_bytes(3)));

file_put_contents($file, json_encode([
    'at' => date('c'),
    'headers' => $headers,
    'body' => file_get_contents('php://input'),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

error_log('caught -> '.basename($file));

header('Content-Type: application/json');
echo '{"status":"ok"}';
