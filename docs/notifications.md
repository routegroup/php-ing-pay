# Notifications

## Introduction

ING Pay tells your server when something happens to a transaction. It sends a `POST` with a JSON body to your notification address, signed with your service key. This is how you learn that a [transaction](/transactions) settled, that a [refund](/refunds) went through, or that a [saved card](/payment-profiles) was charged. This guide covers checking that a notification is genuine, reading it, and the one kind that works differently: report notifications.

## Receiving a Notification

Every notification carries an `X-Imoje-Signature` header. It names the merchant and service the notification was issued for, and holds a hash of the body made with your service key. Check it before you believe anything in the body. In plain PHP, read the body with `file_get_contents('php://input')` and the header from `$_SERVER['HTTP_X_IMOJE_SIGNATURE']`, then pass both to a `Validator`:

::: code-group
<<< @/../tests/Docs/samples/notifications/verify.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/notifications/verify.php#sample [Laravel]
:::

`verifySignature` throws an `InvalidSignatureException` when the hash does not match, when the header names a hash algorithm it does not know, or when the header names a merchant or service other than yours. Answer with an error and do nothing else.

> [!NOTE]
> The hash covers the body byte for byte. Verify the body exactly as it arrived: decoding and re-encoding it changes spacing or escaping, and the hash stops matching.

## Reading a Notification

A sale's notification carries the `transaction` and its `payment`. A refund's notification also carries `originalTransactionId`. These are the bodies the sandbox sent for a settled BLIK payment and for a partial refund:

::: code-group
<<< @/../tests/Fixtures/Notifications/bodies/blik-settled.json [Sale]
<<< @/../tests/Fixtures/Notifications/bodies/refund-settled.json [Refund]
:::

Once the signature holds, decode the body, check it against the notification schema, and build the class that matches its type. Then answer `{"status":"ok"}`:

::: code-group
<<< @/../tests/Docs/samples/notifications/handle.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/notifications/handle.php#sample [Laravel]
:::

`fromNotification` throws a `SchemaValidationException` when the body is not a notification, for example a `transaction` without its `id`, `amount` or `status`. A `GenericNotification` holds the `transaction`, its `payment`, and the `action` or `paymentProfile` when the gateway sent one. In a `RefundNotification`, `transaction` is the refund itself, of type `refund`, while `originalTransactionId` and `payment` describe the transaction it gave money back from.

Act on `transaction.status`, using the same statuses as when you [read a transaction](/transactions#reading-a-transaction). Fulfil an order when its transaction is `settled`, and find the order through `orderId`, which is the identifier you sent when you created the transaction.

## Report Notifications

ING Pay also tells you when a report file is ready. A `ReportNotification` holds the report's `id`, its `status`, the file's format in `fileExt`, and the `url` to download it from:

::: code-group
<<< @/../tests/Docs/samples/notifications/report.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/notifications/report.php#sample [Laravel]
:::

> [!WARNING]
> Report notifications arrive **unsigned**, at an address of their own. Never route them to the endpoint that verifies signatures, because `verifySignature` rejects them by design. Nothing proves that a report notification came from ING Pay, so treat its `url` as something anyone could have sent you.

## In Laravel

The package binds `RequestValidator` in the container. It takes the framework's `Request` and passes the core `Validator` the raw body from `getContent()` and the `x-imoje-signature` header, so the body is never re-encoded. Inject it into the route or controller that receives notifications. `verifySignature($request)` and `fromNotification($request)` throw the same exceptions as the core, and an exception you do not catch becomes a `500` response.

ING Pay posts from outside your application, without a CSRF token. Put the notification routes in `routes/api.php`, or exclude them in `bootstrap/app.php` with `$middleware->validateCsrfTokens(except: ['ing-pay/*'])`.

## Testing

`Utils::mockHeaders()` signs a payload the way ING Pay would, with your configuration, so a test can send your endpoint a notification that passes verification. Encode the body with the same flags it uses:

::: code-group
<<< @/../tests/Docs/samples/notifications/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/notifications/testing.php#sample [Laravel]
:::

Every notification class has a factory: `GenericNotification::factory()` builds a settled sale (`asPending()` makes it pending), `RefundNotification::factory()` a settled refund, and `ReportNotification::factory()` a generated report.
