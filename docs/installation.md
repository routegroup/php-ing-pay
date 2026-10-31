# Installation

## Introduction

You install one package: the plain-PHP client, or the Laravel layer that brings the client with it. This page covers what each one needs next to it, so that the [configuration](/configuration) and the first [request](/sending-requests) work on the first try.

## Requirements

Both packages need PHP 8.4 or newer. The Laravel package needs Laravel 13: it is tested with Pest 5, which Laravel allows only from 13.

## Installing the Package

To add the package that fits your application:

::: code-group
```sh [PHP]
composer require routegroup/php-ing-pay
```

```sh [Laravel]
composer require routegroup/laravel-ing-pay
```
:::

The core brings one library with it, `justinrainbow/json-schema`, which checks the notifications ING Pay sends you against their schema. The Laravel package brings the core.

> [!WARNING]
> Neither package is tagged on Packagist yet, so these commands do not resolve today. The guides describe the code on `main`, which is what the first release will ship.

## Adding an HTTP Client

The core describes requests and reads responses, but sends nothing by itself. The quickest way to give it a way out is Guzzle, which it wires up in one line. Laravel already ships Guzzle behind its own HTTP client, which is what the Laravel package sends through:

::: code-group
```sh [PHP]
composer require guzzlehttp/guzzle
```

```sh [Laravel]
# Nothing to add: the package sends through Laravel's HTTP client.
```
:::

If your application already has another PSR-18 client, add the two PSR interfaces it speaks instead, with `composer require psr/http-client psr/http-factory`. The core suggests these packages rather than requiring them, so an application that sends some other way installs none of them. [Sending Requests](/sending-requests) shows how each one is plugged in.

## In Laravel

The service provider is discovered automatically, so there is nothing to register. It binds the `Api`, the paywall builder and the notification validator in the container, all built from `services.ing_pay`, which [Configuration](/configuration) fills in.

## Testing

Every request, response and value in the core has a factory that builds a valid example, and the factories generate their data with Faker. The core does not require Faker at runtime, so add it to your development dependencies:

::: code-group
```sh [PHP]
composer require --dev fakerphp/faker
```

```sh [Laravel]
# A new Laravel application has it in require-dev already; this checks.
composer show fakerphp/faker
```
:::

Calling a factory without Faker installed throws a `MissingFakerException` that names the package to add.
