<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Exceptions\InvalidInstallmentException;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Value\Installment;

try {
    $installment = new Installment(['period' => 12]);
    $installment->assertFitsChannel(PaymentMethodCode::INBANK_0);
} catch (InvalidInstallmentException $e) {
    $error = $e->getMessage(); // installment.period: must be at most 10 for inbank_0
}
// #endregion sample

return $error ?? null;
