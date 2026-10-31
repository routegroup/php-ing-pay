# Payment Profiles

## Introduction

A payment profile is a card that a payer saved with ING Pay for one-click payments. ING Pay keeps the card. You keep the profile's ID and the customer ID the card was saved under, and you can charge the card later without the payer entering it again. Cards are saved through the [card widget](/card-widget-and-one-click#showing-the-widget), under the `customerId` you give it.

## Listing a Customer's Cards

To show a customer the cards they saved, ask for their profiles by your customer ID:

::: code-group
<<< @/../tests/Docs/samples/payment-profiles/list.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/payment-profiles/list.php#sample [Laravel]
:::

Each `PaymentProfile` has its `id`, the card's `organization` (such as `VISA`), its `maskedNumber`, and its expiry in `month` and `year`. `merchantCustomerId` is the customer ID you asked with. Only offer the profiles that are `isActive`.

## Charging a Profile

To charge a saved card, pass a `ChargeProfileRequest` with the profile's ID, the amount, the currency and your order's identifier to `chargeProfile`:

::: code-group
<<< @/../tests/Docs/samples/payment-profiles/charge.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/payment-profiles/charge.php#sample [Laravel]
:::

The payer does nothing, so there is nowhere to send them. The response holds the `transaction` that was charged. In the sandbox it comes back already `settled`, and a single notification follows, with the profile in `transaction.paymentProfile`:

<<< @/../tests/Fixtures/Notifications/bodies/profile-charge-settled.json

As with any transaction, fulfil the order when that [notification](/notifications) says it is `settled`.

## Deactivating a Profile

When the payer removes a card, deactivate its profile. Read it back to see the change:

::: code-group
<<< @/../tests/Docs/samples/payment-profiles/deactivate.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/payment-profiles/deactivate.php#sample [Laravel]
:::

A deactivated profile is still there, with `isActive` set to `false`. `deactivateProfileByPost()` does the same with a `DeactivateProfileRequest`, through a `POST` instead of a `DELETE`.

## In Laravel

Resolve the `Api` from the container, or inject it. The requests and responses are the same classes as in plain PHP.

## Testing

Answer the charge with `ChargeProfileResponse::factory()`, then check what was sent:

::: code-group
<<< @/../tests/Docs/samples/payment-profiles/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/payment-profiles/testing.php#sample [Laravel]
:::

`ProfilesResponse::factory()` and `ProfileResponse::factory()` build the answers to listing, reading and deactivating.
