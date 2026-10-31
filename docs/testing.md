# Testing

## Introduction

Your tests should never reach ING Pay. The `Api` sends through a client you hand it, and the package ships `FakeClient`, which answers with what you give it and keeps every request it was sent. In Laravel, the `Api` sends through Laravel's HTTP client, so `Http::fake()` and `Http::assertSent()` work as they do for any other call. Factories, covered under [Payloads](/payloads#building-payloads-with-factories), build the answers; they need `fakerphp/faker`, which [Installation](/installation#testing) adds.

## Faking the Gateway

Build an `Api` over a `FakeClient` that answers with a factory-built response, call your code, then check what was sent:

::: code-group
<<< @/../tests/Docs/samples/testing/fake.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/testing/fake.php#sample [Laravel]
:::

`FakeClient::answering()` gives the same answer to every call, with a status of 200 unless you pass another. `lastRequest()` is the last request sent, with its `method`, `url`, `headers` and `body`, the JSON array after your merchant and service were filled in. `sent()` lists them all, and `sentCount()` counts them.

## Answering in Order

When your code makes several calls, queue an answer for each, refusals included:

::: code-group
<<< @/../tests/Docs/samples/testing/queue.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/testing/queue.php#sample [Laravel]
:::

Queued answers are used first, in order, then the one given to `answering()`, if any. A fake with nothing left to answer throws a `RuntimeException` naming the request, so a call you did not expect fails the test instead of getting an invented answer. [Error Handling](/error-handling#testing) shows how to assert on what a refusal carries.

> [!NOTE]
> A response's `toArray()` is its hydrated fields, which match what ING Pay sends for almost every call. `getPaymentMethods` answers with an envelope instead, so hand `FakeClient` that factory's own answer: `new FakeClient(PaymentMethodsResponse::factory()->make()->http())`.

## Sharing Your Configuration

A factory generates its own merchant and service IDs, which will not match the configuration your code runs with. Give every factory yours, in one place:

::: code-group
<<< @/../tests/Docs/samples/testing/shared-state.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/testing/shared-state.php#sample [Laravel]
:::

`Factory::useSharedState()` applies its values to every payload any factory builds, before the factory's own `state()` and the values you pass to `make()`, so either can still override it. A key a payload does not have is ignored. Pass a closure, as here, to have it read again for every payload. The state is static: set it in a `beforeEach` and clear it with `Factory::flushSharedState()` in an `afterEach`, or it leaks into the next test.

## In Laravel

The package does the configuration sharing for you: while tests run, every factory carries `services.ing_pay.merchant_id` and `services.ing_pay.service_id`.

`Http::fake()` also catches any other HTTP call your test makes, so give the gateway its own URL pattern if you fake others. To use `FakeClient` instead, bind it as the `Client` before the `Api` is resolved, with `app()->instance(Client::class, $client)`; the rest of this page then applies as written.
