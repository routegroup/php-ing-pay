<?php

declare(strict_types=1);

/** @var Config $config */

// #region sample
use JsonSchema\Validator as JsonSchemaValidator;
use Routegroup\IngPay\Exceptions\InvalidSignatureException;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Lib\Validator;
use Routegroup\IngPay\Notification\GenericNotification;

$notification = GenericNotification::factory()->make();

$body = json_encode($notification->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
$headers = new Utils($config)->mockHeaders($notification);

$validator = new Validator(new JsonSchemaValidator, new Utils($config), $config);

expect(fn () => $validator->verifySignature($body, $headers['x-imoje-signature']))
    ->not->toThrow(InvalidSignatureException::class);
// #endregion sample

return $body;
