<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\ServiceResponseFactory;
use Routegroup\IngPay\Value\ServiceData;

/**
 * @method static ServiceResponseFactory factory()
 */
class ServiceResponse extends BaseResponse
{
    use HasFactory;

    public readonly ServiceData $service;
}
