<?php

declare(strict_types=1);

/** @var TransactionResponse $response */

// #region sample
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Types\ActionType;

$action = $response->action;

$next = match ($action?->type) {
    ActionType::REDIRECT => ['redirect', $action->url],
    ActionType::TRANSFER => ['show', "Pay to {$action->ban}, title: {$action->title}"],
    null => ['wait', null],
};
// #endregion sample

return $next;
