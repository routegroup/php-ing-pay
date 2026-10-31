<?php

declare(strict_types=1);

/**
 * @var Config $config
 * @var string $body
 * @var string $signature
 */

// #region sample
use JsonSchema\Validator as JsonSchemaValidator;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Lib\Validator;

$validator = new Validator(new JsonSchemaValidator, new Utils($config), $config);

$validator->verifySignature($body, $signature);
// #endregion sample

return $validator;
