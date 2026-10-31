# Card Widget & One-Click

## Introduction

The card widget is a card form that ING Pay renders inside your own page. The payer types their card details into it, so they never reach your server, and the widget can save the card for later. Once a card is saved, you can charge it again with one call and no form at all: that is one-click. The widget is built from the same signed fields as the [paywall](/paywall), rendered as `data-*` attributes of a script tag. If you would rather send the payer away to pay, the paywall or a [transaction](/transactions) does that.

## Showing the Widget

To show the widget, pass a `OneClickRequest` to `fields` and write each field as a `data-*` attribute of ING Pay's widget script. The request names the payer by `customerId`, your own identifier for them, which is what the saved card is filed under:

::: code-group
<<< @/../tests/Docs/samples/card-widget-and-one-click/widget.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/card-widget-and-one-click/widget.php#sample [Laravel]
:::

Each field name becomes a kebab-case attribute: `customerId` is `data-customer-id`. The script must carry the ID in `OneClickRequest::SCRIPT_ID`, because the widget will not start under any other, and `widgetUrl()` points it at the sandbox or production widget, whichever environment you configured.

`widgetType` defaults to `ONECLICK`. The other types are listed under [`WidgetType`](/reference/enums#WidgetType).

> [!NOTE]
> `locale`, `inline`, `elementId` and `elementEvent` set up the widget itself, so they are left out of the signature. You can change them without signing again; every other field must reach the widget exactly as `fields` returned it.

## Charging a Saved Card

When the payer comes back, look up the cards saved under their `customerId`:

::: code-group
<<< @/../tests/Docs/samples/card-widget-and-one-click/profiles.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/card-widget-and-one-click/profiles.php#sample [Laravel]
:::

Each card is a `PaymentProfile`. Its `maskedNumber`, `organization` and expiry are safe to show, and its `id` is what you charge. To charge the card the payer picked, pass its `id` to `chargeProfile`:

::: code-group
<<< @/../tests/Docs/samples/card-widget-and-one-click/charge.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/card-widget-and-one-click/charge.php#sample [Laravel]
:::

There is no redirect and no form: the response carries the `transaction` the charge created, with its status. Fulfil the order when its [notification](/notifications) says it is `settled`. [Payment Profiles](/payment-profiles) covers saved cards in full, including deactivating one.

## In Laravel

`Paywall` and `Api` are both bound in the container and built from `services.ing_pay`. Pass the signed fields to a Blade view that writes the script tag, and resolve `Api` with `app(Api::class)` for the one-click calls.

## Testing

The widget makes no call from your server, so test the fields it gets. Because the presentation fields are unsigned, two requests that differ only in `locale` carry the same signature:

::: code-group
<<< @/../tests/Docs/samples/card-widget-and-one-click/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/card-widget-and-one-click/testing.php#sample [Laravel]
:::

The one-click calls go through `Api`, so they are tested like any other call: `ProfilesResponse::factory()` and `ChargeProfileResponse::factory()` give the fake its answers. [Testing](/testing) covers the fake client in full.
