# Payment Links

## Introduction

A payment link is a paywall page that ING Pay keeps for you. You create it from your server, get back its address, and send that address to the payer however you like: in an email, a chat, an invoice or a QR code. The payer opens it when they are ready, picks a method and pays. Unlike the [paywall](/paywall), the link exists on ING Pay's side, so you can read it back and cancel it. If the payer is on your checkout right now, the paywall or a [transaction](/transactions) gets them there without the extra step. [Choosing an Integration](/choosing-an-integration) compares them.

## Creating a Link

To create a link, pass a `CreatePaymentRequest` to `createPayment`. It needs the amount, the currency, your order's identifier and the payer:

::: code-group
<<< @/../tests/Docs/samples/payment-links/create.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/payment-links/create.php#sample [Laravel]
:::

The response holds the `payment` that was created. Its `url` is the link to send, and its `id` is how you read or cancel it later, so store it next to your order.

> [!NOTE]
> Amounts are integers in the currency's smallest unit. `4999` in `PLN` is 49.99 zł.

## Reading a Link

To see where a link stands, ask for it by its ID:

::: code-group
<<< @/../tests/Docs/samples/payment-links/read.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/payment-links/read.php#sample [Laravel]
:::

The answer is the payment itself, not wrapped in `payment` as on creation. A new link is `new`. `transactions` lists the transactions the payer started from it, and `isUsed` says whether they have used it. Every status is listed under [`TransactionStatus`](/reference/enums#TransactionStatus).

> [!WARNING]
> A payer arriving at your `successReturnUrl` has not proved anything: the address can be opened by hand. Fulfil the order when ING Pay's signed [notification](/notifications) says the transaction is `settled`, or when you have read the link back yourself.

## Cancelling a Link

When the order is cancelled or paid some other way, cancel the link so nobody pays it twice:

::: code-group
<<< @/../tests/Docs/samples/payment-links/cancel.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/payment-links/cancel.php#sample [Laravel]
:::

The response is the link as it is now, with its status `cancelled`. `isActive` can stay `true` after a cancel, so check `status`, not `isActive`.

## In Laravel

The `Api` is bound in the container and built from `services.ing_pay`. Resolve it with `app(Api::class)` or inject it, and use `route()` for the addresses the payer returns to. Because requests go through Laravel's HTTP client, `Http::fake()` intercepts them in your tests.

## Testing

Your code depends on `Api`, so a test builds one over a `FakeClient` that answers with a factory-made response and records what was sent:

::: code-group
<<< @/../tests/Docs/samples/payment-links/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/payment-links/testing.php#sample [Laravel]
:::

`PaymentResponse::factory()` answers creation; `GetPaymentResponse::factory()` and `CancelPaymentResponse::factory()` answer the read and the cancel. [Testing](/testing) covers the fake client in full.
