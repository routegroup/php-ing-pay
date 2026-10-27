<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Value;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Value\Browser;

/** @extends Factory<Browser> */
class BrowserFactory extends Factory
{
    protected string $payload = Browser::class;

    protected function definition(): array
    {
        return [
            'ip' => $this->faker()->ipv4(),
            'language' => str_replace('_', '-', $this->faker()->locale()),
            'jsEnabled' => $this->faker()->boolean(),
            // Real UTC offsets in minutes, from Baker Island to Kiritimati.
            'timezoneOffset' => $this->faker()->numberBetween(-12, 14) * 60,
            'userAgent' => $this->faker()->userAgent(),
            'accept' => $this->faker()->randomElement([
                'application/json, text/javascript, */*; q=0.01',
                'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                '*/*',
            ]),
            'javaEnabled' => $this->faker()->boolean(),
            'screenColorDepth' => $this->faker()->randomElement([16, 24, 30, 32, 48]),
            'screenHeight' => $this->faker()->numberBetween(480, 2160),
            'screenWidth' => $this->faker()->numberBetween(640, 3840),
        ];
    }
}
