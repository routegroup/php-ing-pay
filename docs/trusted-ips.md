# Trusted IPs

## Introduction

Your merchant account keeps a list of trusted IP addresses: the addresses ING Pay accepts API calls from. When your servers move, or you add one, the list has to follow, and the API lets you read it and send a new one without opening the merchant panel.

## Reading the List

To see which addresses are trusted, call `getTrustedIps`:

::: code-group
<<< @/../tests/Docs/samples/trusted-ips/read.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/trusted-ips/read.php#sample [Laravel]
:::

`trustedIps` is a list of address strings, or `null` when ING Pay sends none.

## Changing the List

To trust another address, send the list you want with `putTrustedIps`. Start from what you just read, so the addresses already there stay:

::: code-group
<<< @/../tests/Docs/samples/trusted-ips/update.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/trusted-ips/update.php#sample [Laravel]
:::

ING Pay's own documentation calls this both setting the list and adding to it, so send the whole list you want rather than only the new address. The answer carries no list, only a `status`, which is `success` when ING Pay took it; read the list again to see the result.

> [!WARNING]
> The list decides where ING Pay accepts API calls from. Keep the address this call is sent from on it, and try a change on the sandbox first.

## In Laravel

Nothing changes: resolve the `Api` from the container and send the same requests.

## Testing

A test answers with the shape the update returns and checks what was sent:

::: code-group
<<< @/../tests/Docs/samples/trusted-ips/testing.php#sample [PHP]
<<< @/.laravel/tests/Docs/samples/trusted-ips/testing.php#sample [Laravel]
:::

`TrustedIpsResponse::factory()` builds a list of random addresses, as reading does, and `afterPut()` builds the `status`-only answer an update gets.
