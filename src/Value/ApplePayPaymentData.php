<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Exceptions\InvalidWalletException;
use Routegroup\IngPay\Factories\Value\ApplePayPaymentDataFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static ApplePayPaymentDataFactory factory()
 */
class ApplePayPaymentData extends Payload
{
    use HasFactory;

    public readonly string $version;

    public readonly string $data;

    public readonly string $signature;

    public readonly ApplePayHeader $header;

    /** @throws InvalidWalletException */
    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;

        foreach (['version', 'data', 'signature', 'header'] as $field) {
            if (($attributes[$field] ?? null) === null || $attributes[$field] === '') {
                throw new InvalidWalletException("wallet.paymentData.$field", 'is required');
            }
        }

        parent::__construct($attributes);
    }
}
