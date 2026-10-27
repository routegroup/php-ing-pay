<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Exceptions\InvalidWalletException;
use Routegroup\IngPay\Factories\Value\ApplePayHeaderFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static ApplePayHeaderFactory factory()
 */
class ApplePayHeader extends Payload
{
    use HasFactory;

    public const TRANSACTION_ID_LENGTH = 64;

    public readonly string $ephemeralPublicKey;

    public readonly string $publicKeyHash;

    public readonly string $transactionId;

    /** @throws InvalidWalletException */
    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;

        foreach (['ephemeralPublicKey', 'publicKeyHash', 'transactionId'] as $field) {
            if (($attributes[$field] ?? null) === null || $attributes[$field] === '') {
                throw new InvalidWalletException("wallet.paymentData.header.$field", 'is required');
            }
        }

        if (strlen((string) $attributes['transactionId']) < self::TRANSACTION_ID_LENGTH) {
            throw new InvalidWalletException(
                'wallet.paymentData.header.transactionId',
                'must be at least '.self::TRANSACTION_ID_LENGTH.' characters',
            );
        }

        parent::__construct($attributes);
    }
}
