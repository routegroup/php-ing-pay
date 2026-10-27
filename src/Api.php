<?php

declare(strict_types=1);

namespace Routegroup\IngPay;

use Routegroup\IngPay\Api\CancelPaymentRequest;
use Routegroup\IngPay\Api\CanRefundRequest;
use Routegroup\IngPay\Api\ChargeProfileRequest;
use Routegroup\IngPay\Api\CreatePaymentRequest;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Api\DeactivateProfileRequest;
use Routegroup\IngPay\Api\GetPaymentMethodsRequest;
use Routegroup\IngPay\Api\PutTrustedIpsRequest;
use Routegroup\IngPay\Api\RefundRequest;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\Request as HttpRequest;
use Routegroup\IngPay\Http\Response as HttpResponse;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Response\CancelPaymentResponse;
use Routegroup\IngPay\Response\CanRefundResponse;
use Routegroup\IngPay\Response\ChargeProfileResponse;
use Routegroup\IngPay\Response\GetPaymentResponse;
use Routegroup\IngPay\Response\GetTransactionResponse;
use Routegroup\IngPay\Response\PaymentMethodsResponse;
use Routegroup\IngPay\Response\PaymentResponse;
use Routegroup\IngPay\Response\ProfileResponse;
use Routegroup\IngPay\Response\ProfilesResponse;
use Routegroup\IngPay\Response\RefundResponse;
use Routegroup\IngPay\Response\ServiceResponse;
use Routegroup\IngPay\Response\ServicesResponse;
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Response\TrustedIpsResponse;

/**
 * The gateway's JSON interface. It holds configuration and a client, builds
 * each call from Url, and hands back a typed response.
 */
class Api
{
    public function __construct(
        private readonly Client $client,
        private readonly Config $config,
        public readonly Url $url,
    ) {}

    public function createTransaction(CreateTransactionRequest $request): TransactionResponse
    {
        return new TransactionResponse($this->send(HttpRequest::post($this->url->createTransactionUrl()), $request));
    }

    public function getTransaction(string $transactionId): GetTransactionResponse
    {
        return new GetTransactionResponse($this->send(HttpRequest::get($this->url->createGetTransactionUrl($transactionId)), null, ['transactionId' => $transactionId]));
    }

    public function createPayment(CreatePaymentRequest $request): PaymentResponse
    {
        return new PaymentResponse($this->send(HttpRequest::post($this->url->createPaymentUrl()), $request));
    }

    public function getPayment(string $paymentId): GetPaymentResponse
    {
        return new GetPaymentResponse($this->send(HttpRequest::get($this->url->createGetPaymentUrl($paymentId)), null, ['paymentId' => $paymentId]));
    }

    public function cancelPayment(CancelPaymentRequest $request): CancelPaymentResponse
    {
        return new CancelPaymentResponse($this->send(HttpRequest::post($this->url->createCancelPaymentUrl()), $request));
    }

    public function createRefund(RefundRequest $request, string $transactionId): RefundResponse
    {
        return new RefundResponse($this->send(HttpRequest::post($this->url->createRefundUrl($transactionId)), $request));
    }

    public function getProfile(string $profileId): ProfileResponse
    {
        return new ProfileResponse($this->send(HttpRequest::get($this->url->createProfileIdUrl($profileId)), null, ['profileId' => $profileId]));
    }

    public function chargeProfile(ChargeProfileRequest $request): ChargeProfileResponse
    {
        return new ChargeProfileResponse($this->send(HttpRequest::post($this->url->createChargeProfileUrl()), $request));
    }

    public function deactivateProfile(string $profileId): ProfileResponse
    {
        return new ProfileResponse($this->send(HttpRequest::delete($this->url->createProfileIdUrl($profileId)), null, ['profileId' => $profileId]));
    }

    public function captureTransaction(string $transactionId): TransactionResponse
    {
        return new TransactionResponse($this->send(HttpRequest::post($this->url->createCaptureUrl($transactionId)), null, ['transactionId' => $transactionId]));
    }

    public function voidTransaction(string $transactionId): TransactionResponse
    {
        return new TransactionResponse($this->send(HttpRequest::post($this->url->createVoidUrl($transactionId)), null, ['transactionId' => $transactionId]));
    }

    public function getProfilesByCid(string $cid): ProfilesResponse
    {
        return new ProfilesResponse($this->send(HttpRequest::get($this->url->createProfileCidUrl($cid)), null, ['cid' => $cid]));
    }

    public function deactivateProfileByPost(DeactivateProfileRequest $request): ProfileResponse
    {
        return new ProfileResponse($this->send(HttpRequest::post($this->url->createProfileDeactivateUrl()), $request));
    }

    public function canRefund(CanRefundRequest $request, string $transactionId): CanRefundResponse
    {
        return new CanRefundResponse($this->send(HttpRequest::post($this->url->createCanRefundUrl($transactionId)), $request));
    }

    public function getPaymentMethods(GetPaymentMethodsRequest $request, string $serviceId): PaymentMethodsResponse
    {
        return new PaymentMethodsResponse($this->send(HttpRequest::post($this->url->createGetPaymentMethodsUrl($serviceId)), $request));
    }

    public function getService(string $serviceId): ServiceResponse
    {
        return new ServiceResponse($this->send(HttpRequest::get($this->url->createServiceUrl($serviceId)), null, ['serviceId' => $serviceId]));
    }

    public function getServices(): ServicesResponse
    {
        return new ServicesResponse($this->send(HttpRequest::get($this->url->createServicesUrl()), null, []));
    }

    public function getTrustedIps(): TrustedIpsResponse
    {
        return new TrustedIpsResponse($this->send(HttpRequest::get($this->url->createSettingsIpsUrl()), null, []));
    }

    public function putTrustedIps(PutTrustedIpsRequest $request): TrustedIpsResponse
    {
        return new TrustedIpsResponse($this->send(HttpRequest::put($this->url->createSettingsIpsUrl()), $request));
    }

    /**
     * Deliberately without retries. The specification answers a 500 with
     * "disable the channel for a few minutes" and a 503 or 504 with a single
     * GET five minutes later, not with another attempt — and repeating a POST
     * the gateway may already have accepted risks a second transaction.
     *
     * @param  array<array-key, mixed>  $context  what to put in an exception when there is no request body
     */
    private function send(HttpRequest $http, (Payload&Identified)|null $request = null, array $context = []): HttpResponse
    {
        if ($request !== null) {
            $context = $request->withIdentity($this->config)->toArray();
            $http = new HttpRequest($http->method, $http->url, $context, $http->headers);
        }

        $response = $this->client->send(
            $http->withHeaders(['Authorization' => 'Bearer '.$this->config->apiKey])
        );

        $this->guard($response, $context);

        return $response;
    }

    /**
     * @param  array<array-key, mixed>  $context
     *
     * @throws ApiErrorException
     */
    private function guard(HttpResponse $response, array $context): void
    {
        if ($response->successful()) {
            return;
        }

        /** Without the flag only 422 and 500 raise, as they always have. */
        if ($this->config->strictErrors || in_array($response->status, [422, 500], true)) {
            throw new ApiErrorException($response, $context);
        }
    }
}
