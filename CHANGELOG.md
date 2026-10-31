# Changelog

All notable changes to `routegroup/php-ing-pay` will be documented in this file.

## v1.0.0 - 2026-10-31

First release of the ING Pay client as a plain-PHP package, split out of [`routegroup/laravel-imoje`](https://github.com/routegroup/laravel-imoje).

- Nothing from Laravel: the API client, the payloads, the signed paywall form and notification verification all run on plain PHP 8.4.
- HTTP goes through a one-method client interface, with a PSR-18 adapter included; `Psr18Client::guzzle()` wires Guzzle up in one line.
- Payloads are typed `readonly` classes hydrated from the gateway's arrays. The declared type is the cast, an unknown key from the gateway is ignored, and a missing non-nullable field throws.
- Test factories ship with the package. Faker stays a dev dependency, so the package has no runtime dependency beyond the JSON schema validator.
- Namespace `Routegroup\IngPay\`.

Documentation: https://php-ing-pay.routegroup.pl
