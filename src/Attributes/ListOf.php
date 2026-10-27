<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Attributes;

use Attribute;

/**
 * Names the element type of a list field.
 *
 * A declared `array` carries no element type, so this is the one thing the
 * property's own type cannot say. An attribute rather than the `@var` docblock:
 * opcache may be configured to strip comments, and a payment library must not
 * hydrate differently depending on that setting.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class ListOf
{
    /** @param class-string $type */
    public function __construct(public string $type) {}
}
