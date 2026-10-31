# Refunds

## Introduction

A refund gives the payer back all or part of a transaction they paid. The refund is a transaction of its own, of type `refund`, linked to the one it refunds. Like any transaction it finishes later, and a [notification](/notifications#reading-a-notification) tells you when it does.

## Checking a Refund

Before you offer a refund, ask whether the amount can go back. Pass a `CanRefundRequest` and the ID of the transaction to `canRefund`:

::: code-group
<<< @/../tests/Docs/samples/refunds/can-refund.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/refunds/can-refund.php#sample [Laravel]
:::

`refundable` is the answer. `partialRefund` holds the smallest and the largest amount that a partial refund of this transaction may have, in `minRefundAmount` and `maxRefundAmount`.

## Refunding a Transaction

To refund, pass a `RefundRequest` with the amount and a title to `createRefund`, together with the ID of the transaction you are refunding:

::: code-group
<<< @/../tests/Docs/samples/refunds/refund.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/refunds/refund.php#sample [Laravel]
:::

The request sets its type to `refund` itself, and your service ID is added from your configuration as it is sent. The response holds the refund's own `transaction`. Store its `id` next to the order, because that is the ID the refund's notifications carry.

> [!NOTE]
> The amount is in the currency's smallest unit, like every amount: `2000` in `PLN` is 20.00 zł.

## Learning the Outcome

A refund starts as `new` and ends as `settled`. ING Pay notifies you of each change with a [`RefundNotification`](/notifications#reading-a-notification). Its `transaction` is the refund, and its `originalTransactionId` is the transaction you refunded.

When you [read the original transaction back](/transactions#reading-a-transaction), its `refunds` lists the refunds made against it. The specification does not define the shape of those entries, so they come back as plain arrays.

## In Laravel

Resolve the `Api` from the container, or inject it. The requests and responses are the same classes as in plain PHP.

## Testing

Answer the refund call with `RefundResponse::factory()`, then check what was sent:

::: code-group
<<< @/../tests/Docs/samples/refunds/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/refunds/testing.php#sample [Laravel]
:::

`CanRefundResponse::factory()` builds a refundable answer, with a partial-refund range up to the full amount.
