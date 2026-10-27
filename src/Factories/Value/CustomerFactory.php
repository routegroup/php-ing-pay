<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\Customer;

/** @extends Factory<Customer> */
class CustomerFactory extends Factory
{
    protected string $payload = Customer::class;

    protected function definition(): array
    {
        return [
            'firstName' => $this->faker()->firstName(),
            'lastName' => $this->faker()->lastName(),
            'email' => $this->faker()->email(),
            'phone' => $this->faker()->numerify('#########'),
        ];
    }

    public function asCard(): static
    {
        return $this->state([
            'cid' => $this->faker()->numerify('##############'),
            'company' => $this->faker()->company(),
        ]);
    }
}
