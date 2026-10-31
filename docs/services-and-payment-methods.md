# Services & Payment Methods

## Introduction

Your ING Pay merchant account holds one or more services, usually one per shop, and each service has its own set of payment methods switched on. The API can tell you which services you have, which methods a service accepts, and which of them a payer can use for a given amount right now. That last answer is what a method picker on your checkout is built from, before you start a [transaction](/transactions). [Choosing an Integration](/choosing-an-integration) explains when you need one.

## Listing Your Services

To see every service on your merchant account, call `getServices`:

::: code-group
<<< @/../tests/Docs/samples/services-and-payment-methods/services.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/services-and-payment-methods/services.php#sample [Laravel]
:::

Each entry is a `ServiceData` with its `id`, its `created` timestamp, whether it `isActive`, and its `paymentMethods`. The `id` is the service ID your [configuration](/configuration#your-credentials) names; the requests you send are filled in with it on their way out.

## Reading a Service

To see the payment methods one service has switched on, ask for it by its ID:

::: code-group
<<< @/../tests/Docs/samples/services-and-payment-methods/service.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/services-and-payment-methods/service.php#sample [Laravel]
:::

Every `ServicePaymentMethod` names its `paymentMethod` and `paymentMethodCode`, its `currency`, whether it `isActive` on the service and `isOnline` at ING Pay, and its `transactionLimits`, whose `minTransaction` and `maxTransaction` each carry a `value`. The logos are in `paymentMethodImage` and `paymentMethodCodeImage`.

## Payment Methods for a Checkout

To build a method picker, ask which methods can take this payment. `getPaymentMethods` takes the amount and currency, and the service to ask about:

::: code-group
<<< @/../tests/Docs/samples/services-and-payment-methods/payment-methods.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/services-and-payment-methods/payment-methods.php#sample [Laravel]
:::

The answer is a list of `PaymentMethodGroup`s, such as "Płatność BLIK" or "Przelew tradycyjny", each with a `label`, a `priority`, an `image` and its `channels`. A channel is what the payer actually picks: its `paymentMethod` and `paymentMethodCode` are the two values a [`CreateTransactionRequest`](/transactions#creating-a-transaction) needs. Sort groups and channels by `priority` to show them in the order ING Pay suggests. The request also takes an optional `device` (`desktop` or `mobile`) and `locale`.

> [!NOTE]
> `isOnline` can be `false` for a method that is switched on in your service: ING Pay marks a method offline while it is unavailable. Hide those channels, or show them disabled.

## In Laravel

The service your application is configured for is `services.ing_pay.service_id`, so the samples read it with `config()->string()`. Everything else is the same classes.

## Testing

A test answers with a factory-built service and checks which one was asked for:

::: code-group
<<< @/../tests/Docs/samples/services-and-payment-methods/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/services-and-payment-methods/testing.php#sample [Laravel]
:::

`getPaymentMethods` answers with a `{timestamp, data}` envelope, and its factory copies a real sandbox answer. `toArray()` gives the hydrated fields instead of that envelope, so hand the fake the factory's own answer: `new FakeClient(PaymentMethodsResponse::factory()->make()->http())`. [Testing](/testing) covers the fake in full.
