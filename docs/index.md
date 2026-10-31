---
layout: home

hero:
  name: ING Pay for PHP
  text: Payments through ING Pay, from plain PHP or Laravel.
  tagline: Typed requests and responses, a signed paywall, verified notifications, and samples that the test suite runs.
  actions:
    - theme: brand
      text: Get Started
      link: /installation
    - theme: alt
      text: Introduction
      link: /introduction
    - theme: alt
      text: View on GitHub
      link: https://github.com/routegroup/php-ing-pay

features:
  - icon: 🧭
    title: Choose an integration
    details: Paywall, payment link, an API transaction or the card widget. Compare the four ways to take a payment before you build one.
    link: /choosing-an-integration
    linkText: Compare them
  - icon: 💳
    title: Take a payment
    details: Start a transaction from your own server, BLIK with a code included, and send the payer on to finish it.
    link: /transactions
    linkText: Transactions
  - icon: 🔔
    title: Handle notifications
    details: Check that what ING Pay sends your server is genuine, then read how the transaction, refund or card charge ended.
    link: /notifications
    linkText: Notifications
  - icon: ↩️
    title: Refund, capture and void
    details: Give the payer all or part of their money back, or capture or void a payment that was only authorized.
    link: /refunds
    linkText: Refunds
  - icon: 🧩
    title: Plain PHP or Laravel
    details: Every sample comes in a PHP and a Laravel tab. Pick yours once and the whole site follows.
    link: /sending-requests
    linkText: Sending Requests
  - icon: 🧪
    title: Tested, offline
    details: A FakeClient and a factory for every response. Every sample here runs in the packages' own test suites.
    link: /testing
    linkText: Testing
---

## Install

To add the package that fits your application:

::: code-group
```sh [PHP]
composer require routegroup/php-ing-pay
```

```sh [Laravel]
composer require routegroup/laravel-ing-pay
```
:::

Then follow [Installation](/installation) to add an HTTP client, and [Configuration](/configuration) to give the package your credentials.
