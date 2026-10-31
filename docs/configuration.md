# Configuration

## Introduction

Everything the package knows about your account sits in one `Config` object: who you are at ING Pay, the two secrets that prove it, and which of ING Pay's environments you talk to. Every other guide assumes you have one. In plain PHP you build it yourself; in Laravel it is built for you from `config/services.php`.

## Your Credentials

ING Pay gives you four values, and the package uses each for one job:

- The **merchant ID** names your account. It is part of every API address.
- The **service ID** names the shop, or service, a payment belongs to. It is filled into every request you send and every paywall form you build.
- The **service key** signs the [paywall](/choosing-an-integration#paywall) form and checks the signature on every notification ING Pay sends you.
- The **API key** is the bearer token on every API call.

Keep them out of your code and in your environment:

```dotenv
ING_PAY_MERCHANT_ID=your-merchant-id
ING_PAY_SERVICE_ID=your-service-id
ING_PAY_SERVICE_KEY=your-service-key
ING_PAY_API_KEY=your-api-key
ING_PAY_ENV=sandbox
```

## Building the Config

To build the configuration from those variables:

::: code-group
<<< @/../tests/Docs/samples/configuration/config.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/configuration/services.php#sample [Laravel]
:::

In PHP, `$config` is what you pass on to the `Api` in [Sending Requests](/sending-requests). In Laravel, the Laravel tab is your `config/services.php`, or the `ing_pay` entry added to the one you have; the package reads it whenever it builds the `Api`.

The environment is either `sandbox` or `production`. `sandbox` sends every call to ING Pay's test gateway and every payer to its test paywall; `production` is the real one. Any other value is refused: in PHP, `Environment::from()` throws a `ValueError`, and in Laravel an `InvalidEnvironmentException` names the value it got.

> [!WARNING]
> In Laravel, an `ING_PAY_ENV` that is missing or empty means `production`, because `.env` turns an empty line into an empty string, not into nothing. Set `ING_PAY_ENV=sandbox` explicitly wherever you are not taking real payments.

## Optional Settings

Three more settings have defaults that suit most applications:

- **Strict errors** (`strictErrors`, or `strict_errors` in Laravel) is on by default, and then every answer outside the 2xx range throws an `ApiErrorException`. Turned off, only a 422 or a 500 throws; any other refusal comes back as a response, whose `failed()` and `error()` you check yourself. [Sending Requests](/sending-requests#when-the-gateway-refuses) covers the exception.
- **Timeout** (`timeout`) is how long a call may take in total, 30 seconds by default.
- **Connect timeout** (`connectTimeout`, or `connect_timeout` in Laravel) is how long opening the connection may take, 10 seconds by default.

The two timeouts are read by the Laravel package's HTTP client. In plain PHP the HTTP client is yours, so its timeouts are set where you create it, as [Sending Requests](/sending-requests#building-the-api) shows.

## In Laravel

The `Config` in the container is built from `services.ing_pay` each time it is resolved, and the `Api`, the paywall builder and the notification validator all take it from there. Call `env()` only inside `config/services.php`: once you run `php artisan config:cache`, `.env` is no longer read anywhere else.

## Testing

A test talks to a [fake client](/sending-requests#testing), never to ING Pay, so it needs no real credentials. Any values will do:

::: code-group
<<< @/../tests/Docs/samples/configuration/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/configuration/testing.php#sample [Laravel]
:::

In Laravel, your test environment's configuration applies, so put sandbox values in `.env.testing` or `phpunit.xml`, and change a single value with `config()` before the code under test resolves anything.
