<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use Routegroup\IngPay\Exceptions\MissingFakerException;
use Routegroup\IngPay\Payload;

/**
 * @template TPayload of Payload
 *
 * @phpstan-consistent-constructor
 */
abstract class Factory
{
    /** @var class-string<TPayload> */
    protected string $payload;

    protected string $locale = 'en_US';

    protected int $count = 1;

    /** @var list<array<string, mixed>> */
    protected array $states = [];

    /** @var list<array<string, mixed>> */
    protected array $sequence = [];

    private ?Generator $faker = null;

    public static function new(): static
    {
        // PHPStan cannot carry TPayload through new static(). The template is
        // load-bearing everywhere else, so the limitation is absorbed here.
        // @phpstan-ignore return.type
        return new static;
    }

    /** @return array<string, mixed> */
    abstract protected function definition(): array;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function state(array $attributes): static
    {
        $clone = clone $this;
        $clone->states[] = $attributes;

        return $clone;
    }

    public function count(int $times): static
    {
        $clone = clone $this;
        $clone->count = max(1, $times);

        return $clone;
    }

    /**
     * Cycles the given states across the payloads that makeMany() builds, so a
     * set of them differs in a way the test chose rather than only at random.
     *
     * @param  array<string, mixed>  ...$states
     */
    public function sequence(array ...$states): static
    {
        $clone = clone $this;
        $clone->sequence = array_values($states);

        return $clone;
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return TPayload
     */
    public function make(array $attributes = []): Payload
    {
        return $this->build(0, $attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return list<TPayload>
     */
    public function makeMany(?int $times = null, array $attributes = []): array
    {
        $times = max(1, $times ?? $this->count);

        return array_map(fn (int $index): Payload => $this->build($index, $attributes), range(0, $times - 1));
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return TPayload
     */
    private function build(int $index, array $attributes): Payload
    {
        $values = $this->definition();

        foreach ($this->states as $state) {
            $values = array_replace($values, $state);
        }

        if ($this->sequence !== []) {
            $values = array_replace($values, $this->sequence[$index % count($this->sequence)]);
        }

        return new $this->payload(array_replace($values, $attributes));
    }

    protected function faker(): Generator
    {
        if (! class_exists(FakerFactory::class)) {
            throw new MissingFakerException;
        }

        return $this->faker ??= FakerFactory::create($this->locale);
    }
}
