# ING Pay for PHP

A client for the ING Pay payment gateway, written in plain PHP with no framework
dependency.

Using Laravel? Install [`routegroup/laravel-ing-pay`][laravel] instead — it wraps this
package with a service provider, configuration binding and test factories.

> **Status: under construction.** This package is being split out of
> [`routegroup/laravel-imoje`][old] and is **not yet usable**. The model, the HTTP
> client and signature verification have not landed. What is here is the foundation:
> the enumerations, the exceptions, configuration and URL building — all tested.

## Requirements

PHP 8.4 or newer. Nothing else: this package has no runtime dependencies.

## Factories in your own tests

Every value has a factory, and `Payload::factory()` resolves it by convention:

```php
$transaction = Transaction::factory()->make();
$several = Transaction::factory()->makeMany(3);
```

Factories need `fakerphp/faker`, which this package does not require at runtime —
add it to your own `require-dev`.

A factory knows nothing about your merchant, and generates an identifier rather
than inventing one you would recognise. To build payloads that carry your own
configuration, hand the base a resolver once, during test bootstrapping:

```php
use Routegroup\IngPay\Factories\Factory;

Factory::useSharedState(fn (): array => [
    'merchantId' => $yourMerchantId,
    'serviceId' => $yourServiceId,
]);
```

Every payload built afterwards carries those values, and keys a payload does not
declare are ignored, so one resolver serves the whole model. A `state()` or an
argument to `make()` still wins over them, in that order. Pass `null` to clear it.

The resolver runs per payload rather than once, so it may read whatever the
surrounding application holds at that moment. In Laravel that is `config()`; the
companion package wires it for you.

## Development

```
composer test        # lint, types and tests — the same gate CI runs
composer test:lint   # Rector and Pint, checking only
composer test:types  # PHPStan
composer test:unit   # Pest
composer lint        # Rector and Pint, fixing in place
```

Everything runs in the project's container, for example
`docker compose run --rm app composer test`.

## Licence

MIT. See [LICENSE.md](LICENSE.md).

[laravel]: https://github.com/routegroup/laravel-ing-pay
[old]: https://github.com/routegroup/laravel-imoje
