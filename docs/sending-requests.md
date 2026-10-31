# Sending Requests

## Introduction

Every call to ING Pay goes through one object, the `Api`. It holds your [configuration](/configuration) and a client that does the HTTP, and it has a method for each call, such as `createTransaction` or `getTransaction`, which takes a typed request and hands back a typed response. This page sets the `Api` up and shows what every call has in common. The guides that follow only call it.

## Building the Api

To build the `Api` over Guzzle, with the `$config` from [Configuration](/configuration):

::: code-group
<<< @/../tests/Docs/samples/sending-requests/api.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/sending-requests/api.php#sample [Laravel]
:::

`Psr18Client::guzzle()` takes Guzzle's own options, which is where the timeouts go. It needs `guzzlehttp/guzzle` installed, and throws a `MissingGuzzleException` that says so if it is not.

If your application already has another PSR-18 client, wrap that instead: `new Psr18Client($client, $requestFactory, $streamFactory)` takes the client and the PSR-17 factories for requests and streams, which most PSR-7 libraries ship as a single object.

In Laravel the `Api` is bound in the container. Resolve it with `app(Api::class)`, or type-hint it in a controller or a job.

## What Every Call Does

When you call a method on the `Api`, it:

- adds your merchant and service ID to the request, so you never set them yourself;
- sends the request as JSON, with your API key as its bearer token;
- reads the answer into the method's response class, such as `TransactionResponse`, with typed properties and [enums](/reference/enums) in place of strings;
- throws an `ApiErrorException` if ING Pay refused the call.

It does not retry. ING Pay's specification answers a 500 by asking you to pause for a few minutes, and a 503 or 504 with a single read five minutes later, and repeating a request that ING Pay may already have accepted could open a second transaction. If a call fails on the way, read the transaction back before you try again.

### The Client Seam

The client is anything that implements `Routegroup\IngPay\Http\Client`, which has a single method, `send(Request $request): Response`. The request carries a method, a URL, a body and headers; the response carries the status, the raw body, the headers and the reason phrase. `Psr18Client` is one implementation, the Laravel package's `LaravelClient` is another, and `FakeClient` is the one your [tests](#testing) use. Implement the interface yourself to send through anything else.

## When the Gateway Refuses

A refused call throws an `ApiErrorException`. Its `status()` is the HTTP status, so you can tell a missing transaction from a broken request:

::: code-group
<<< @/../tests/Docs/samples/sending-requests/errors.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/sending-requests/errors.php#sample [Laravel]
:::

The exception's message is ING Pay's own, when it gave one. Its `error` holds the rest of ING Pay's answer, with a `code` and a list of `errors`, each naming the field that was wrong; its `request` holds what you sent. `context()` returns all three as an array, ready for your log. [Error Handling](/error-handling) lists every exception the package throws.

With [strict errors](/configuration#optional-settings) turned off, only a 422 or a 500 throws. Any other refusal comes back as a response, and its `failed()` and `error()` tell you what happened.

## In Laravel

The `Api` sends through Laravel's HTTP client, with the timeouts from `services.ing_pay`. That means Laravel's HTTP client events and global middleware see every call to ING Pay, and `Http::fake()` intercepts them in your tests.

An `ApiErrorException` you do not catch reaches Laravel's exception handler, which logs its `context()` with it, so the log entry shows what was sent and what ING Pay answered.

## Testing

Your code depends on the `Api`, not on the network, so a test builds the `Api` over a `FakeClient`. `FakeClient::answering()` answers every call with the same body. `queue()` adds answers that are given out in order, each with its own status. The fake keeps every request it was sent:

::: code-group
<<< @/../tests/Docs/samples/sending-requests/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/sending-requests/testing.php#sample [Laravel]
:::

`sent()` lists every request, `lastRequest()` gives the most recent and `sentCount()` counts them. A fake with nothing left to answer throws instead of making something up, so a call you did not expect fails the test. [Testing](/testing) covers the fake in full.

In Laravel, put the fake in the container in place of the `Client`, and every `Api` resolved afterwards sends to it. `Http::fake()` works too, as the [Transactions guide](/transactions#testing) shows.
