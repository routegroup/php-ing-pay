# API Calls

Every method on `Routegroup\IngPay\Api`, with what it takes, what it returns and the request it sends. Request classes live in `Routegroup\IngPay\Api` and responses in `Routegroup\IngPay\Response`. Paths are relative to `https://api.pay.ing.pl/v1`, or `https://api.sandbox.pay.ing.pl/v1` in the sandbox, and `{merchantId}` comes from your [configuration](/configuration).

| Method | Takes | Returns | Sends | Guide |
| --- | --- | --- | --- | --- |
| `createTransaction` | `CreateTransactionRequest` | `TransactionResponse` | `POST /merchant/{merchantId}/transaction` | [Transactions](/transactions#creating-a-transaction) |
| `getTransaction` | `string $transactionId` | `GetTransactionResponse` | `GET /merchant/{merchantId}/transaction/{transactionId}` | [Transactions](/transactions#reading-a-transaction) |
| `captureTransaction` | `string $transactionId` | `TransactionResponse` | `POST /merchant/{merchantId}/transaction/{transactionId}/capture` | [Capture & Void](/capture-and-void#capturing-a-transaction) |
| `voidTransaction` | `string $transactionId` | `TransactionResponse` | `POST /merchant/{merchantId}/transaction/{transactionId}/void` | [Capture & Void](/capture-and-void#voiding-a-transaction) |
| `canRefund` | `CanRefundRequest`, `string $transactionId` | `CanRefundResponse` | `POST /merchant/{merchantId}/transaction/{transactionId}/can-refund` | [Refunds](/refunds#checking-a-refund) |
| `createRefund` | `RefundRequest`, `string $transactionId` | `RefundResponse` | `POST /merchant/{merchantId}/transaction/{transactionId}/refund` | [Refunds](/refunds#refunding-a-transaction) |
| `createPayment` | `CreatePaymentRequest` | `PaymentResponse` | `POST /merchant/{merchantId}/payment` | [Payment Links](/payment-links#creating-a-link) |
| `getPayment` | `string $paymentId` | `GetPaymentResponse` | `GET /merchant/{merchantId}/payment/{paymentId}` | [Payment Links](/payment-links#reading-a-link) |
| `cancelPayment` | `CancelPaymentRequest` | `CancelPaymentResponse` | `POST /merchant/{merchantId}/payment/cancel` | [Payment Links](/payment-links#cancelling-a-link) |
| `getProfilesByCid` | `string $cid` | `ProfilesResponse` | `GET /merchant/{merchantId}/profile/cid/{cid}` | [Payment Profiles](/payment-profiles#listing-a-customer-s-cards) |
| `getProfile` | `string $profileId` | `ProfileResponse` | `GET /merchant/{merchantId}/profile/id/{profileId}` | [Payment Profiles](/payment-profiles#deactivating-a-profile) |
| `chargeProfile` | `ChargeProfileRequest` | `ChargeProfileResponse` | `POST /merchant/{merchantId}/transaction/profile` | [Payment Profiles](/payment-profiles#charging-a-profile) |
| `deactivateProfile` | `string $profileId` | `ProfileResponse` | `DELETE /merchant/{merchantId}/profile/id/{profileId}` | [Payment Profiles](/payment-profiles#deactivating-a-profile) |
| `deactivateProfileByPost` | `DeactivateProfileRequest` | `ProfileResponse` | `POST /merchant/{merchantId}/profile/deactivate` | [Payment Profiles](/payment-profiles#deactivating-a-profile) |
| `getServices` | nothing | `ServicesResponse` | `GET /merchant/{merchantId}/services` | [Services & Payment Methods](/services-and-payment-methods#listing-your-services) |
| `getService` | `string $serviceId` | `ServiceResponse` | `GET /merchant/{merchantId}/service/{serviceId}` | [Services & Payment Methods](/services-and-payment-methods#reading-a-service) |
| `getPaymentMethods` | `GetPaymentMethodsRequest`, `string $serviceId` | `PaymentMethodsResponse` | `POST /merchant/{merchantId}/service/{serviceId}/get-payment-methods` | [Services & Payment Methods](/services-and-payment-methods#payment-methods-for-a-checkout) |
| `getTrustedIps` | nothing | `TrustedIpsResponse` | `GET /merchant/{merchantId}/settings/ips` | [Trusted IPs](/trusted-ips#reading-the-list) |
| `putTrustedIps` | `PutTrustedIpsRequest` | `TrustedIpsResponse` | `PUT /merchant/{merchantId}/settings/ips` | [Trusted IPs](/trusted-ips#changing-the-list) |

Every call carries your API key as a bearer token, adds your merchant and service ID to the request, and is sent once, without retries. [Sending Requests](/sending-requests#what-every-call-does) explains what happens on the way, and [Error Handling](/error-handling) what happens when the gateway refuses.

## Paywall

`Routegroup\IngPay\Paywall` is not an API call: it sends nothing, and signs fields that the payer's browser carries to ING Pay. Its requests live in `Routegroup\IngPay\Paywall`.

| Method | Takes | Returns | Guide |
| --- | --- | --- | --- |
| `url` | `CreateTransactionRequest`, optional `Lang` | the paywall address as a `string` | [Paywall](/paywall#sending-the-payer-to-the-paywall) |
| `fields` | `CreateTransactionRequest` | the signed fields as an `array` | [Paywall](/paywall#posting-a-form-instead) |
| `fields` | `OneClickRequest` | the widget's signed fields | [Card Widget & One-Click](/card-widget-and-one-click#showing-the-widget) |
| `fields` | `InstallmentsCalculatorRequest` | the calculator's signed fields | [Installments](/installments#showing-the-calculator) |
