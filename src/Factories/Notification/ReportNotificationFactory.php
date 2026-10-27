<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Factories\Notification;

use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Notification\ReportNotification;

/** @extends Factory<ReportNotification> */
class ReportNotificationFactory extends Factory
{
    protected string $payload = ReportNotification::class;

    protected function definition(): array
    {
        return [
            'id' => $this->faker()->unique()->uuid(),
            'status' => 'generated',
            'fileExt' => $this->faker()->randomElement(['csv', 'owi1', 'mt940']),
            'created' => $this->faker()->unixTime(),
            'modified' => $this->faker()->unixTime(),
            'url' => $this->faker()->url(),
        ];
    }
}
