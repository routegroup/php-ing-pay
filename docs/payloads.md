# Payloads

## Introduction

Every request you send, every response you get back and every value inside them, from a `Customer` to a `Transaction`, is a payload: a class whose typed, read-only properties are the fields ING Pay knows. This page covers what they all share: how they are built from arrays, how they read ING Pay's answers, how they turn back into arrays, and how factories build them for your tests. The guide for each task explains the fields its payloads carry.

## Building a Payload

A payload is built from an array whose keys are its field names. A nested array becomes the payload its field is typed as, and an enum field takes either the case or its value:

::: code-group
<<< @/../tests/Docs/samples/payloads/build.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/payloads/build.php#sample [Laravel]
:::

`$request->currency` is now `Currency::PLN` and `$request->customer` a `Customer`, so you can pass whichever form you have at hand. A field you leave out is `null`. A field the payload requires, such as a customer's `firstName`, throws `MissingFieldException` when it is missing, naming the payload and the field, before anything is sent.

`toArray()`, and `json_encode()`, which calls it, give the payload back as ING Pay's JSON: enums as their values, nested payloads as arrays, and `null` fields left out. Note the `type` of `sale` in it: a `CreateTransactionRequest` sets that default itself.

> [!NOTE]
> Payloads are read-only. To change a field, build a new payload from `toArray()` and the new value.

## Reading a Response

A response is a payload too, built from what ING Pay answered:

::: code-group
<<< @/../tests/Docs/samples/payloads/read.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/payloads/read.php#sample [Laravel]
:::

Wire values are converted to the declared type, so an amount is an `int` and a status a `TransactionStatus`. A status or code this package does not know yet reads as `null` rather than failing the response, so compare against the cases you handle. `http()` keeps the answer exactly as it arrived, with its `status`, `body` and `header()`, for when you need something the typed fields leave out.

## Building Payloads With Factories

Every payload has a factory that builds a valid one with random values, for your tests and seeders. `factory()` finds it by convention:

::: code-group
<<< @/../tests/Docs/samples/payloads/factories.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/payloads/factories.php#sample [Laravel]
:::

`make()` builds one payload, and the array you pass it wins over everything else. `state()` sets values for every payload the factory builds, and takes a closure when a value depends on the ones generated. `makeMany()` builds several, as many as `count()` says when you give no number, and `sequence()` cycles its states across them. A response's factory also takes `status()`, to build a response ING Pay refused.

> [!WARNING]
> Factories need `fakerphp/faker`, which the package does not require at runtime. Add it to your `require-dev`, or a factory throws `MissingFakerException`.

## In Laravel

While your tests run, every factory fills in the merchant and service from `services.ing_pay`, so `$ours` is `true`. Outside tests the factories leave them random. [Testing](/testing#sharing-your-configuration) shows how the same works in plain PHP.
