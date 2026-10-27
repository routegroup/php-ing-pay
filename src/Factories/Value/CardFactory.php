<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\Card;

/** @extends Factory<Card> */
class CardFactory extends Factory
{
    protected string $payload = Card::class;

    protected function definition(): array
    {
        $expirationDate = $this->faker()->creditCardExpirationDate();

        return [
            'firstName' => $this->faker()->firstName(),
            'lastName' => $this->faker()->lastName(),
            'number' => $this->faker()->creditCardNumber(),
            'month' => $expirationDate->format('m'),
            'year' => $expirationDate->format('Y'),
            'cvv' => $this->faker()->numerify('###'),
        ];
    }
}
