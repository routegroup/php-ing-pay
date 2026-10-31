# Choosing an Integration

## Introduction

ING Pay can take a payment in four ways, and choosing one is the first real decision of an integration. They differ in who picks the payment method, where the payer pays, and how much of the checkout you build. They agree on the end: however a payment starts, ING Pay tells your server how it ended with a signed notification, and you fulfil the order from that.

## At a Glance

| | Paywall | Payment link | Transaction | Card widget |
| --- | --- | --- | --- | --- |
| The payer picks the method | on ING Pay's page | on ING Pay's page | on your checkout | — (cards only) |
| The payer pays | on ING Pay's page | on ING Pay's page, whenever they open the link | at their bank, or in their BLIK app | on your page |
| You call | `Paywall::url()` | `Api::createPayment()` | `Api::createTransaction()` | `Paywall::fields()` |
| Your server proves itself with | the service key, which signs the address | the API key | the API key | the service key, which signs the fields |
| You build | a redirect | a message with a link | a method picker and the next step | a page that loads ING Pay's script |

## Paywall

The paywall is ING Pay's own checkout page. You send the payer there with the amount, the order and where to come back to, and they choose a method and pay on that page. Nothing is sent from your server: the `Paywall` class builds a signed address, and the payer's browser does the rest.

Choose the paywall when you want a working checkout with the least code, and are happy for the payer to leave your site to pay.

## Payment Link

A payment link is a payment page that ING Pay hosts for you. You create it with an API call, and ING Pay answers with its address, which you send to the payer however you like: in an e-mail, an invoice, a chat. The payer opens it whenever they are ready, until it expires or you cancel it.

Choose a payment link when the payer is not on your site at the moment you ask for the money, such as an order taken by phone or an invoice due in two weeks.

## Transaction

A transaction is a payment you start from your server with the method already chosen, such as a particular bank or BLIK. ING Pay answers with what the payer has to do next, usually a redirect to their bank. You build the method picker yourself, and list what your service accepts with `getPaymentMethods`.

Choose a transaction when the method picker is part of your own checkout. The [Transactions guide](/transactions) walks through it.

## Card Widget

The card widget is a card form that ING Pay's script draws on your own page, so the payer types their card details without leaving your site and without those details passing through your server. You sign its settings with `Paywall::fields()` and a `OneClickRequest`, and put them on the script tag. Besides a single payment, the widget can save the card for one-click or recurring payments later.

Choose the widget when you take cards and want the payer to stay on your page.

## In Laravel

All four work the same way in Laravel. `Api` and `Paywall` are both bound in the container, built from `services.ing_pay`, so you resolve whichever you need and nothing else changes.

## Testing

None of the four needs ING Pay to be tested. The paywall and the widget only build signed fields, which a test reads directly. The payment link and the transaction are API calls, which a test sends to a [fake client](/sending-requests#testing).
