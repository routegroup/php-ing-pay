<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Response;

use Routegroup\IngPay\Concerns\HasFactory;
use Routegroup\IngPay\Factories\Response\TransactionResponseFactory;
use Routegroup\IngPay\Value\Action;
use Routegroup\IngPay\Value\Transaction;

/**
 * @method static TransactionResponseFactory factory()
 */
class TransactionResponse extends BaseResponse
{
    use HasFactory;

    public readonly Transaction $transaction;

    public readonly Action $action;
}
