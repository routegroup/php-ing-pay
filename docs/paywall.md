# Paywall

## Introduction

The paywall is ING Pay's own payment page. You send the payer there with the amount and the order, and they pick a method, pay, and come back to you. Nothing is sent from your server: you build a signed set of fields and the payer's browser carries them to ING Pay, either in a link or in a form. If you want to choose the method on your own checkout instead, start a [transaction](/transactions); if the payer should pay later, from an email or a chat, send a [payment link](/payment-links). [Choosing an Integration](/choosing-an-integration) compares them.

## Sending the Payer to the Paywall

To build the address, pass a paywall `CreateTransactionRequest` to `url`. It takes the amount, the currency, your order's identifier and the payer's name, and optionally the addresses the payer returns to:

::: code-group
<<< @/../tests/Docs/samples/paywall/redirect.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/paywall/redirect.php#sample [Laravel]
:::

`Paywall` adds your merchant and service ID from your configuration and signs every field with your service key, so the gateway can tell the request came from you and was not changed on the way. The key itself never leaves your server. The URL points at the sandbox or production paywall, whichever environment you configured.

> [!NOTE]
> This `CreateTransactionRequest` lives in `Routegroup\IngPay\Paywall`, not in `Routegroup\IngPay\Api`. The paywall names its fields differently (`customerFirstName`, `urlSuccess`), so the two classes are not interchangeable.

### Choosing the Language

To open the paywall in a given language, pass a `Lang` as the second argument. It becomes part of the path:

::: code-group
<<< @/../tests/Docs/samples/paywall/language.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/paywall/language.php#sample [Laravel]
:::

## Posting a Form Instead

If you would rather have the payer submit a form than follow a link, `fields` returns the same signed fields as an array. Render them as hidden inputs of a form that posts to the paywall:

::: code-group
<<< @/../tests/Docs/samples/paywall/form.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/paywall/form.php#sample [Laravel]
:::

The signature covers every field in the array. Render them as they are: a field you change or leave out makes the gateway refuse the payment.

> [!WARNING]
> A payer arriving at your `urlSuccess` has not proved anything: the address can be opened by hand. Fulfil the order when ING Pay's signed [notification](/notifications) says the transaction is `settled`.

## In Laravel

`Paywall` is bound in the container and built from `services.ing_pay`, like `Api`. Resolve it with `app(Paywall::class)` or inject it, and return `redirect()` with its URL from your controller. The request classes and fields are the same as in plain PHP.

## Testing

The paywall makes no call of its own, so there is nothing to fake. Build the URL in your test and read the fields back from its query string:

::: code-group
<<< @/../tests/Docs/samples/paywall/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/paywall/testing.php#sample [Laravel]
:::

`CreateTransactionRequest::factory()` fills every required field, so a test sets only the ones it is about. [Testing](/testing) covers factories in full.
