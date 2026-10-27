<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Exceptions\InvalidWalletException;
use Routegroup\IngPay\Factories\Value\WalletFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static WalletFactory factory()
 */
class Wallet extends Payload
{
    use HasFactory;

    public readonly ?string $signature;

    public readonly ?string $protocolVersion;

    public readonly ?string $signedMessage;

    public readonly ?ApplePayPaymentData $paymentData;

    /** @throws InvalidWalletException */
    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;

        if (($attributes['paymentData'] ?? null) === null) {
            foreach (['signature', 'protocolVersion', 'signedMessage'] as $field) {
                if (($attributes[$field] ?? null) === null || $attributes[$field] === '') {
                    throw new InvalidWalletException("wallet.$field", 'is required');
                }
            }
        }

        parent::__construct($attributes);
    }
}
