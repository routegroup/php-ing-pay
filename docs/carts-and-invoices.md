# Carts & Invoices

## Introduction

Some payments need to know what is being bought, not just how much it costs. ING Lease Now leases the goods to the payer, so it needs the list of items: that is the cart. ING Pay can also issue the payer an invoice for the purchase, which needs the buyer and the invoice lines. Both are value objects you attach to a request you already know: a [payment link](/payment-links), a [transaction](/transactions) or the [paywall](/paywall).

## Sending a Cart

To offer Lease Now, build a `Cart` of `CartItem`s and attach it to a payment link, with Lease Now preselected:

::: code-group
<<< @/../tests/Docs/samples/carts-and-invoices/cart.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/carts-and-invoices/cart.php#sample [Laravel]
:::

Every item needs its `id`, `name`, `amount`, `tax`, `taxStake`, `quantity`, `url` and `categoryId`; `unit`, `state` and `discount` are optional. `amount` and `tax` are in the currency's smallest unit, like every amount.

`taxStake` is a [`LeaseTaxStake`](/reference/enums#LeaseTaxStake), and it also accepts `23` or the invoice's `TAX_23` spelling. A `discount` takes its `amount` and its `tax` together, never one alone.

## Sending an Invoice

To have an invoice issued, build an `Invoice` with the buyer and its positions, and attach it to the transaction:

::: code-group
<<< @/../tests/Docs/samples/carts-and-invoices/invoice.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/carts-and-invoices/invoice.php#sample [Laravel]
:::

The buyer needs `type`, `email`, `fullName`, `street`, `city`, `postalCode` and `countryCodeAlpha2`. A `COMPANY` buyer also needs `idType` and `idNumber`, and a `VAT_ID` needs `idCountryCodeAlpha2` as well. Each position needs `name`, `code`, `quantity`, `unit`, `grossAmount` and a [`TaxStake`](/reference/enums#TaxStake), which also accepts a bare `23`.

The same `Invoice` can go on a `chargeProfile` request, when a [saved card](/card-widget-and-one-click#charging-a-saved-card) pays.

## On the Paywall

The paywall request takes both, as values. `Paywall` encodes each one into a single field before it signs them, so you attach them the same way:

::: code-group
<<< @/../tests/Docs/samples/carts-and-invoices/paywall.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/carts-and-invoices/paywall.php#sample [Laravel]
:::

## When a Cart or Invoice Is Incomplete

A `Cart` or `Invoice` checks itself as it is built, so a missing field fails in your code, before anything is sent. The exception names the field:

::: code-group
<<< @/../tests/Docs/samples/carts-and-invoices/invalid.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/carts-and-invoices/invalid.php#sample [Laravel]
:::

A cart throws `InvalidCartException` and an invoice `InvalidInvoiceException`. Both carry the offending `field` and say what is wrong with it in the message.

## In Laravel

Nothing changes: `Cart` and `Invoice` are plain value objects, attached to requests you send with `app(Api::class)` or build with `app(Paywall::class)`.

## Testing

`Cart::factory()` and `Invoice::factory()` build valid values, so a test attaches one and checks it was sent:

::: code-group
<<< @/../tests/Docs/samples/carts-and-invoices/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/carts-and-invoices/testing.php#sample [Laravel]
:::
