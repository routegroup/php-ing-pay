# Introduction

## Introduction

ING Pay, formerly imoje, is ING's payment gateway: it takes card, BLIK, pay-by-link and wallet payments for your shop and tells your server how each one ended. This site shows you how to talk to it from PHP. Every guide is a task, such as taking a payment, reacting to a notification or refunding, and every sample on it is run by the packages' own test suites, so what you copy is what passes.

## Two Packages

The gateway is covered by two packages, and you install exactly one of them.

[`routegroup/php-ing-pay`](https://github.com/routegroup/php-ing-pay) is the client itself, in plain PHP with no framework behind it. It describes every request, reads every response into a typed object, signs the paywall form the payer's browser submits, and verifies the notifications ING Pay sends you. It sends nothing by itself: you give it a client, or wrap the PSR-18 client you already have.

[`routegroup/laravel-ing-pay`](https://github.com/routegroup/laravel-ing-pay) is the Laravel layer on top of it. It builds the client from your `services.ing_pay` configuration, sends through Laravel's HTTP client, and adds a request validator for notifications. Everything else, from requests to responses to enums, is the core package's classes.

To install the one that fits your application:

::: code-group
```sh [PHP]
composer require routegroup/php-ing-pay
```

```sh [Laravel]
composer require routegroup/laravel-ing-pay
```
:::

Both need PHP 8.4 or newer. The Laravel package needs Laravel 13.

> [!WARNING]
> Neither package is tagged on Packagist yet, so these commands do not resolve today. The guides describe the code on `main`, which is what the first release will ship.

## Reading the Guides

Every sample on this site comes in a `[PHP]` and a `[Laravel]` tab. Pick the one you work in once: every other sample on the site follows, and the choice is remembered on your next visit.

The guides follow the order you will need them in: set the package up, take a payment, then handle what happens after it. Each one starts with what the task is, walks through it section by section, and ends with how to test it.

## In Laravel

The Laravel tab is usually the PHP tab without the setup: the `Api` comes from the container rather than being built by hand. Where Laravel changes more than that, such as configuration, `Http::fake()` or validating a notification request, a guide says so in its own `## In Laravel` section.

## Testing

Neither package needs a network to be tested against. The core ships a `FakeClient` that answers with what you queue and records what it was sent, and a factory for every response, so a test builds a valid answer in one line. In Laravel, `Http::fake()` does the same job. Every guide's last section shows the test for the code above it.
