<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Types\ActionType;
use Routegroup\IngPay\Value\Action;

/** @extends Factory<Action> */
class ActionFactory extends Factory
{
    protected string $payload = Action::class;

    protected function definition(): array
    {
        return [
            'type' => $this->faker()->randomElement(ActionType::cases()),
            'url' => $this->faker()->url(),
            'method' => $this->faker()->randomElement(['GET', 'POST']),
            'contentType' => 'application/x-www-form-urlencoded',
            'contentBodyRaw' => $this->faker()->words(3, true),
            'ban' => $this->faker()->numerify(str_repeat('#', 26)),
            'title' => $this->faker()->words(2, true),
        ];
    }
}
