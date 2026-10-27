<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Notification;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Notification\ReportNotificationFactory;
use Routegroup\IngPay\Payload;

/**
 * @method static ReportNotificationFactory factory()
 */
class ReportNotification extends Payload
{
    use HasFactory;

    public readonly ?string $id;

    public readonly ?string $status;

    public readonly ?string $fileExt;

    public readonly ?int $created;

    public readonly ?int $modified;

    public readonly ?string $url;
}
