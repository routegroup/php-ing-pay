<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Exceptions\InvalidWalletException;
use Routegroup\IngPay\Factories\Value\BrowserFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static BrowserFactory factory()
 */
class Browser extends Payload
{
    use HasFactory;

    public readonly string $ip;

    public readonly string $language;

    public readonly bool $jsEnabled;

    public readonly int $timezoneOffset;

    public readonly string $userAgent;

    public readonly string $accept;

    public readonly bool $javaEnabled;

    public readonly int $screenColorDepth;

    public readonly int $screenHeight;

    public readonly int $screenWidth;

    /** The gateway types these strictly: '100' and 'true' are both refused. */

    /** @throws InvalidWalletException */
    public function __construct($attributes = [])
    {
        $attributes = (array) $attributes;

        foreach ([
            'ip', 'language', 'jsEnabled', 'timezoneOffset', 'userAgent',
            'accept', 'javaEnabled', 'screenColorDepth', 'screenHeight', 'screenWidth',
        ] as $field) {
            if (($attributes[$field] ?? null) === null || $attributes[$field] === '') {
                throw new InvalidWalletException("additionalData.browser.$field", 'is required');
            }
        }

        parent::__construct($attributes);
    }
}
