# Capture & Void

## Introduction

A transaction in status `authorized` has been approved, but the payer's money has not been taken yet: the transaction waits for you to decide. Capturing it takes the money. Voiding it cancels the payment instead. Both act on a transaction you [created](/transactions#creating-a-transaction) earlier, by its ID.

## Capturing a Transaction

To take the money, pass the transaction's ID to `captureTransaction`. Check that the transaction is still `authorized` first:

::: code-group
<<< @/../tests/Docs/samples/capture-and-void/capture.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/capture-and-void/capture.php#sample [Laravel]
:::

The response holds the transaction as it stands after the capture. Read its `status` before you fulfil the order.

> [!NOTE]
> Both calls take the transaction's ID and nothing else. There is no request body to send.

## Voiding a Transaction

To cancel the payment instead, pass the ID to `voidTransaction`:

::: code-group
<<< @/../tests/Docs/samples/capture-and-void/void.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/capture-and-void/void.php#sample [Laravel]
:::

As with a capture, the response holds the transaction as it now stands.

## In Laravel

Resolve the `Api` from the container, or inject it. The calls and responses are the same as in plain PHP.

## Testing

Answer the call with `TransactionResponse::factory()`, then check where it went:

::: code-group
<<< @/../tests/Docs/samples/capture-and-void/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/capture-and-void/testing.php#sample [Laravel]
:::

A capture and a void go to `/transaction/{id}/capture` and `/transaction/{id}/void`, both as a `POST`.
