# Installments

## Introduction

ING Pay can split a purchase into monthly instalments through a lender. It takes two steps. First you show ING Pay's calculator on your checkout, where the payer picks a lender's channel and a number of months. Then you start a [transaction](/transactions) with that choice.

## Showing the Calculator

The calculator is a script that draws into an element on your page. Sign its fields with `Paywall::fields`, then pass them to the calculator once the script has loaded:

::: code-group
<<< @/../tests/Docs/samples/installments/calculator.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/installments/calculator.php#sample [Laravel]
:::

`InstallmentsCalculatorRequest` takes the amount and defaults the currency to `PLN`; your merchant and service ID are added as it is signed. The element and the script must carry the IDs in `WRAPPER_ID` and `SCRIPT_ID`, and `installmentsUrl()` points at the sandbox or production script, whichever environment you configured.

The request class names the range of amounts the calculator covers: `InstallmentsCalculatorRequest::MIN_AMOUNT` to `MAX_AMOUNT`, 300 zł to 50,000 zł.

When the payer settles on a plan, the calculator posts a message to your page whose data carries the `channel` and the `period`. Listen for it and keep both with the order:

```js
window.addEventListener('message', (event) => {
  if (event.data?.channel && event.data?.period) {
    document.querySelector('[name=channel]').value = event.data.channel
    document.querySelector('[name=period]').value = event.data.period
  }
})
```

## Creating the Transaction

To start the instalment payment, create a transaction with `PaymentMethod::IMOJE_INSTALLMENTS`, the payer's channel as `paymentMethodCode`, and the period in `installment`:

::: code-group
<<< @/../tests/Docs/samples/installments/create.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/installments/create.php#sample [Laravel]
:::

The response is the same as for any transaction: send the payer on as its `action` says, as [Transactions](/transactions#sending-the-payer-on) shows.

> [!WARNING]
> The channel and period come from the payer's browser. Check them before you send them, as below, and fulfil the order only when the transaction is `settled`.

### Checking the Period

An `Installment` refuses a period below 3 or above 36 months as it is built. Each channel has its own ceiling, 36 months for `inbank` and 10 for `inbank_0`, and `assertFitsChannel` checks the period against the channel the payer picked:

::: code-group
<<< @/../tests/Docs/samples/installments/period.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/installments/period.php#sample [Laravel]
:::

Both checks throw `InvalidInstallmentException`, whose message names the rule that was broken.

## In Laravel

`Paywall` and `Api` are bound in the container and built from `services.ing_pay`. Pass the calculator's signed fields to a Blade view, and resolve `Api` with `app(Api::class)` to create the transaction.

## Testing

Creating the transaction is an ordinary `Api` call, so a test answers it with a `FakeClient` and checks what was sent:

::: code-group
<<< @/../tests/Docs/samples/installments/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/installments/testing.php#sample [Laravel]
:::

`InstallmentsCalculatorRequest::factory()` builds a calculator request with an amount inside the allowed range.
