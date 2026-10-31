<?php

declare(strict_types=1);

/** @var Api $api */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Value\ApiError;

try {
    $transaction = $api->getTransaction('5b8d0c4e-1f7a-4c2e-9e3b-2a6f1d9c8e70')->transaction;
} catch (ApiErrorException $exception) {
    $status = $exception->status();
    $message = $exception->getMessage();

    $problems = array_map(
        fn (ApiError $error): string => "{$error->property}: {$error->message}",
        $exception->error->errors,
    );

    $log = $exception->context();
}
// #endregion sample

return [$status ?? null, $message ?? null, $problems ?? [], $log ?? []];
