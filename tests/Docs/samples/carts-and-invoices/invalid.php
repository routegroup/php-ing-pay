<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Exceptions\InvalidCartException;
use Routegroup\IngPay\Value\Cart;

try {
    $cart = new Cart(['items' => [['id' => 'SKU-1', 'name' => 'Laptop 14"']]]);
} catch (InvalidCartException $e) {
    $field = $e->field; // items.amount
}
// #endregion sample

return $field ?? null;
