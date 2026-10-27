<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Concerns;

use Routegroup\IngPay\Factories\Factory;

trait HasFactory
{
    /**
     * Resolved by convention: Routegroup\IngPay\Value\Transaction is built by
     * Routegroup\IngPay\Factories\Value\TransactionFactory. A payload whose
     * factory lives elsewhere overrides newFactory().
     *
     * @return Factory<static>
     */
    public static function factory(): Factory
    {
        return static::newFactory();
    }

    /** @return Factory<static> */
    protected static function newFactory(): Factory
    {
        /** @var class-string<Factory<static>> $factory */
        $factory = str_replace('\\IngPay\\', '\\IngPay\\Factories\\', static::class).'Factory';

        return $factory::new();
    }
}
