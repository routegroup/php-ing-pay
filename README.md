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

## Licence

MIT. See [LICENSE.md](LICENSE.md).

[laravel]: https://github.com/routegroup/laravel-ing-pay
[old]: https://github.com/routegroup/laravel-imoje
