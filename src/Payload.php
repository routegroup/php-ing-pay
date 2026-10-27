<?php

declare(strict_types=1);

namespace Routegroup\IngPay;

use BackedEnum;
use JsonSerializable;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Routegroup\IngPay\Attributes\ListOf;
use Routegroup\IngPay\Exceptions\MissingFieldException;

/**
 * @phpstan-consistent-constructor
 *
 * The declared type of a property is the only description of a field: it says
 * what the field is, whether it may be absent, and how an incoming value is
 * converted. Nothing is declared twice.
 */
abstract class Payload implements JsonSerializable
{
    /** An unknown enum value hydrates to null; a payload may ask to be strict instead. */
    protected bool $strictEnums = false;

    /** @param array<string, mixed> $attributes */
    public function __construct(array $attributes = [])
    {
        foreach ($this->fields() as $field) {
            $name = $field->getName();

            $this->$name = $this->read($field, $attributes[$name] ?? null);
        }
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $values = [];

        foreach ($this->fields() as $field) {
            $value = $this->{$field->getName()};

            if ($value !== null) {
                $values[$field->getName()] = self::flatten($value);
            }
        }

        return $values;
    }

    /** @return list<ReflectionProperty> */
    private function fields(): array
    {
        return array_values(array_filter(
            new ReflectionClass(static::class)->getProperties(ReflectionProperty::IS_PUBLIC),
            static fn (ReflectionProperty $field): bool => ! $field->isStatic(),
        ));
    }

    private function read(ReflectionProperty $field, mixed $value): mixed
    {
        $type = $field->getType();

        if (! $type instanceof ReflectionNamedType) {
            return $value;
        }

        if ($value === null) {
            return $type->allowsNull()
                ? null
                : throw new MissingFieldException(static::class, $field->getName(), $type->getName());
        }

        if ($type->getName() === 'array' && is_array($value)) {
            $element = $this->elementType($field);

            if ($element !== null) {
                return array_map(fn (mixed $item): mixed => $this->convert($element, $item), $value);
            }
        }

        return $this->convert($type->getName(), $value);
    }

    /** @return class-string|null */
    private function elementType(ReflectionProperty $field): ?string
    {
        $attribute = $field->getAttributes(ListOf::class)[0] ?? null;

        return $attribute?->newInstance()->type;
    }

    private function convert(string $type, mixed $value): mixed
    {
        if (is_a($type, self::class, true)) {
            if ($value instanceof $type) {
                return $value;
            }

            // A payload of a neighbouring type converts through its array form,
            // which is how a Billing becomes the Shipping a field asks for.
            /** @var array<string, mixed> $attributes */
            $attributes = match (true) {
                $value instanceof self => $value->toArray(),
                is_array($value) => $value,
                default => [],
            };

            return new $type($attributes);
        }

        if (is_subclass_of($type, BackedEnum::class)) {
            if ($value instanceof $type) {
                return $value;
            }

            $backing = is_int($value) ? $value : (string) $value;

            return $this->strictEnums ? $type::from($backing) : $type::tryFrom($backing);
        }

        return match ($type) {
            'int' => (int) $value,
            'float' => (float) $value,
            'string' => (string) $value,
            'bool' => (bool) $value,
            default => $value,
        };
    }

    private static function flatten(mixed $value): mixed
    {
        return match (true) {
            $value instanceof self => $value->toArray(),
            $value instanceof BackedEnum => $value->value,
            is_array($value) => array_map(self::flatten(...), $value),
            default => $value,
        };
    }
}
