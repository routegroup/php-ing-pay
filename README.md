# ING Pay for PHP

A client for the ING Pay payment gateway, written in plain PHP with no framework
dependency. Using Laravel? Install [`routegroup/laravel-ing-pay`][laravel] instead.

**Documentation: https://php-ing-pay.routegroup.pl**

## Installation

PHP 8.4 or newer. Add the package, and Guzzle to send its requests:

```sh
composer require routegroup/php-ing-pay guzzlehttp/guzzle
```

The package is not tagged on Packagist yet, so this resolves once the first
release is out.

## Usage

```php
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Http\Psr18Client;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\Environment;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;

$config = new Config(
    merchantId: 'your-merchant-id',
    serviceId: 'your-service-id',
    serviceKey: 'your-service-key',
    apiKey: 'your-api-key',
    env: Environment::SANDBOX,
);

$api = new Api(Psr18Client::guzzle(), $config, new Url($config));

$response = $api->createTransaction(new CreateTransactionRequest([
    'amount' => 4999,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1042',
    'paymentMethod' => PaymentMethod::PAY_BY_LINK,
    'paymentMethodCode' => PaymentMethodCode::ING,
    'successReturnUrl' => 'https://shop.example/orders/1042/paid',
    'failureReturnUrl' => 'https://shop.example/orders/1042/failed',
    'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska', 'email' => 'anna@example.com'],
]));

$response->transaction->id;
```

The [documentation][docs] covers the paywall, payment links, the card widget,
notifications, refunds and testing.

## Development

`composer test` runs the same lint, type and test gate as CI, for example
`docker compose run --rm app composer test`.

## Licence

MIT. See [LICENSE.md](LICENSE.md).

[docs]: https://php-ing-pay.routegroup.pl
[laravel]: https://github.com/routegroup/laravel-ing-pay
