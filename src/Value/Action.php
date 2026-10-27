<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Value;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Value\ActionFactory;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\ActionType;

/**
 * @method static ActionFactory factory()
 */
class Action extends Payload
{
    use HasFactory;

    public readonly ?ActionType $type;

    public readonly ?string $url;

    public readonly ?string $method;

    public readonly ?string $contentType;

    public readonly ?string $contentBodyRaw;

    public readonly ?string $ban;

    public readonly ?string $title;
}
