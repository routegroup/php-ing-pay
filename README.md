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

## Talking to the gateway

This package sends nothing by itself. It describes a request and reads a
response, and leaves the sending to a client you give it:

```php
interface Client
{
    public function send(Request $request): Response;
}
```

One method, and no PSR-7 in sight. If you already have a PSR-18 client, wrap it:

```php
$client = new Psr18Client($yourPsr18Client, $yourPsr17Factory, $yourPsr17Factory);
```

That needs `psr/http-client` and `psr/http-factory`, which this package suggests
rather than requires — it has no runtime dependencies of its own.

In your tests, use `FakeClient` instead. It answers without a network and keeps
what it was asked, so you can assert on both sides of the exchange:

```php
$client = FakeClient::answering(['status' => 'settled']);

// ... drive your integration ...

expect($client->lastRequest()->body)->toHaveKey('amount');
```

Running out of queued answers raises rather than inventing one, so a test cannot
pass by accident on a call you did not expect.

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

Factory::useSharedState([
    'merchantId' => $yourMerchantId,
    'serviceId' => $yourServiceId,
]);
```

Every payload built afterwards carries those values, and keys a payload does not
declare are ignored, so one resolver serves the whole model. A `state()` or an
argument to `make()` still wins over them, in that order, and
`Factory::flushSharedState()` clears them again — worth doing in a test
teardown, since the state is static.

Pass a closure instead when the values are not known yet, or change between
payloads — it is resolved once per payload rather than captured:

```php
Factory::useSharedState(fn (): array => ['serviceId' => config('services.ing_pay.service_id')]);
```

In Laravel the companion package wires that for you.

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
