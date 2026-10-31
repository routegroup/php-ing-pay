# Error Handling

## Introduction

Things go wrong in two places. ING Pay can refuse a call, because a field is wrong, a transaction does not exist or the gateway is down, and the package raises `ApiErrorException` for it. Or the package itself can refuse before anything is sent, because a cart, an invoice or another value you built breaks one of the gateway's rules. Every exception the package throws is listed at the [end of this page](#exceptions), and each one that has details to log carries them in `context()`.

## Catching a Refusal

Wrap the call and catch `ApiErrorException`:

::: code-group
<<< @/../tests/Docs/samples/error-handling/catch.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/error-handling/catch.php#sample [Laravel]
:::

`status()` is the HTTP status ING Pay answered with, and the exception's message is the gateway's own `message`, or the HTTP reason or status when it sent none. `error` holds the whole answer as an `ApiErrorResponse`: a `code`, the `message`, and `errors`, a list of `ApiError`s whose `property` names the field that failed. `context()` puts the status, that answer and the request you sent into one array, ready for your log.

> [!WARNING]
> The package never retries. ING Pay's answer to a 500 is to stop calling for a few minutes, and to a 503 or 504 a single `GET` five minutes later; repeating a `POST` it may already have accepted can open a second transaction. Read the transaction back before you try again.

## Reading Refusals Without Exceptions

By default every answer outside 2xx throws. Turn `strictErrors` off in your configuration and only 422 and 500 still throw; any other refusal comes back as a response you can ask:

::: code-group
<<< @/../tests/Docs/samples/error-handling/lenient.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/error-handling/lenient.php#sample [Laravel]
:::

`failed()` and `successful()` look at the status, `http()` is the answer itself, and `error()` reads it as an `ApiErrorResponse`, or gives `null` when the call succeeded.

> [!NOTE]
> A refusal still has to fit the response it is read into. `getTransaction` and every other call whose response requires a field, such as `transaction`, throw `MissingFieldException` on a refusal instead, so this mode only helps with calls whose fields are all optional. Keep `strictErrors` on unless you have a reason.

## Exceptions

Every exception is in `Routegroup\IngPay\Exceptions`, apart from the Laravel package's own.

| Exception | Extends | Thrown when |
| --- | --- | --- |
| `ApiErrorException` | `Exception` | ING Pay answered a call outside 2xx: any such status with `strictErrors` on, only 422 and 500 with it off. Carries `status()`, `error`, `request` and `context()`. |
| `MissingFieldException` | `UnexpectedValueException` | A payload is built without a field its type requires, from your array or from ING Pay's answer. Names the `payload`, `field` and `expected` type. |
| `InvalidCartException` | `InvalidArgumentException` | A `Cart` breaks a rule: no items, an item without a required field, a quantity below 1, a tax rate Lease Now does not take, or an invalid URL. Names the `field`. |
| `InvalidInvoiceException` | `InvalidArgumentException` | An `Invoice`, its buyer, positions or VAT exemption miss a required field or carry a tax rate the gateway does not take. Names the `field`. |
| `InvalidInstallmentException` | `InvalidArgumentException` | An `Installment` period is missing, not a whole number, or outside what ING Pay offers. Carries the `period`. |
| `InvalidMultipayoutException` | `InvalidArgumentException` | A multipayout recipient misses a field, has an amount below 1, or a value too long or with characters the gateway rejects. Names the `index` and `field`. |
| `InvalidWalletException` | `InvalidArgumentException` | A wallet payment, its Apple Pay data or the browser data misses a required field. Names the full `field` path. |
| `InvalidSignatureException` | `Exception` | A notification's signature is missing, does not verify, or was issued for another merchant or service. |
| `SchemaValidationException` | `Exception` | A notification's body does not have the shape ING Pay documents. Lists the failures in `errors`. |
| `MissingFakerException` | `RuntimeException` | A factory runs without `fakerphp/faker` installed. Add it to your `require-dev`. |
| `MissingGuzzleException` | `RuntimeException` | `Psr18Client::guzzle()` runs without `guzzlehttp/guzzle` installed. |
| `InvalidEnvironmentException` (Laravel) | `InvalidArgumentException` | `services.ing_pay.env` is neither `production` nor `sandbox`. Carries the `given` value. |

The `Invalid…` exceptions are thrown while you build the value, so a bad cart fails in your code, not at the gateway. Catch `InvalidArgumentException` to handle all of them at once.

## In Laravel

`strictErrors` is `services.ing_pay.strict_errors`, and it defaults to `true`. Laravel adds an exception's `context()` to the log entry when it reports one, so `report($exception)`, or letting the exception reach the handler, logs the status, ING Pay's answer and your request with no extra code.

## Testing

To test how your code handles a refusal, answer with an `ApiErrorResponse` and the status:

::: code-group
<<< @/../tests/Docs/samples/error-handling/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/error-handling/testing.php#sample [Laravel]
:::

`ApiErrorResponse::factory()` builds a random code, message and one field error; pass what your code reads. [Testing](/testing) covers the fake in full.
