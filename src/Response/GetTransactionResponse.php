<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\GetTransactionResponseFactory;
use Routegroup\IngPay\Value\Action;
use Routegroup\IngPay\Value\Transaction;

/**
 * @method static GetTransactionResponseFactory factory()
 */
class GetTransactionResponse extends BaseResponse
{
    use HasFactory;

    public readonly Transaction $transaction;

    public readonly ?Action $action;
}
