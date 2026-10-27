<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Exceptions\InvalidWalletException;
use Routegroup\IngPay\Factories\Value\AdditionalDataFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static AdditionalDataFactory factory()
 */
class AdditionalData extends Payload
{
    use HasFactory;

    public readonly Browser $browser;

    /** @throws InvalidWalletException */
    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;

        if (! isset($attributes['browser'])) {
            throw new InvalidWalletException('additionalData.browser', 'is required');
        }

        parent::__construct($attributes);
    }
}
