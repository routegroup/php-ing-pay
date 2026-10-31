# Transactions

## Introduction

A transaction is a single payment you start from your own server. You choose the payment method on your checkout, send the amount and the order it pays for, and ING Pay answers with the transaction it opened and, usually, where to send the payer next. The payment itself finishes later, on the payer's side, and you learn the outcome from a [notification](/notifications). If you would rather not build the method picker yourself, a [payment link](/payment-links) or the [paywall](/paywall) hands that part to ING Pay; [Choosing an Integration](/choosing-an-integration) compares them.

## Creating a Transaction

To start a transaction, pass a `CreateTransactionRequest` to `createTransaction`. It needs the amount, the currency, your order's identifier, the payment method, and the two addresses the payer returns to:

::: code-group
<<< @/../tests/Docs/samples/transactions/create.php#sample [PHP]
:::

Your merchant and service ID are added from your [configuration](/configuration) as the request is sent, so the request never carries them. The response holds the `transaction` that was opened, with its `id`. Store it next to your order: it is how you will recognise the transaction in every notification and every later call.

> [!NOTE]
> Amounts are integers in the currency's smallest unit. `4999` in `PLN` is 49.99 zł.

### BLIK With a Code

If your checkout collects the six-digit BLIK code itself, send it as `blikCode`:

::: code-group
<<< @/../tests/Docs/samples/transactions/blik-code.php#sample [PHP]
:::

The payer confirms the payment in their banking app, so there is nowhere to send them, and the response carries no action.

## Sending the Payer On

Most transactions answer with an `action`, which tells you what the payer has to do next. A `redirect` sends them to `url`, usually their bank. A `transfer` asks them to wire the money themselves, to the account in `ban` with the transfer title in `title`. No action at all means there is nothing for the payer to do on your side:

::: code-group
<<< @/../tests/Docs/samples/transactions/action.php#sample [PHP]
:::

> [!WARNING]
> A payer arriving at your `successReturnUrl` has not proved anything: the address can be opened by hand. Fulfil the order when a signed [notification](/notifications) says the transaction is `settled`, or when you have [read it back](#reading-a-transaction) yourself.

## Reading a Transaction

To check where a transaction stands, ask for it by its ID:

::: code-group
<<< @/../tests/Docs/samples/transactions/read.php#sample [PHP]
:::

A new transaction is `pending`. It ends as `settled` once the money is in, as `rejected` if the payer's bank refused it, or as `cancelled` if it expired unpaid. Every status is listed under [`TransactionStatus`](/reference/enums#TransactionStatus).

Reading is the fallback, not the main road. Notifications tell you the moment a status changes, so poll only when one is overdue.

## Testing

Your code depends on `Api`, so a test builds one over a `FakeClient`. The fake answers with what you give it and records what was sent, and every response has a factory that builds a valid answer:

::: code-group
<<< @/../tests/Docs/samples/transactions/testing.php#sample [PHP]
:::

A fake that runs out of answers throws instead of inventing one, so a call you did not expect fails the test. [Testing](/testing) covers queued answers, factories and shared state in full.
