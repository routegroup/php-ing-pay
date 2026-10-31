<?php

declare(strict_types=1);

namespace Routegroup\IngPay;

use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Types\HashMethod;
use Routegroup\IngPay\Types\Lang;
use Routegroup\IngPay\Value\Cart;
use Routegroup\IngPay\Value\Invoice;

/** Builds the signed form the payer's browser submits; Api is its JSON peer. */
class Paywall
{
    public function __construct(
        private readonly Config $config,
        private readonly Utils $utils,
    ) {}

    /** @return array<string, mixed> */
    public function fields(Payload&Identified $request, HashMethod $hashMethod = HashMethod::SHA256): array
    {
        $fields = $request->withIdentity($this->config)->toArray();

        if (($request->invoice ?? null) instanceof Invoice) {
            $fields['invoice'] = $request->invoice->encoded();
        }

        if (($request->cart ?? null) instanceof Cart) {
            $fields['cart'] = $request->cart->encoded();
        }

        unset($fields['signature']);

        $signed = $request instanceof HasUnsignedFields
            ? array_diff_key($fields, array_flip($request->unsignedFields()))
            : $fields;

        $fields['signature'] = $this->utils->createSignature($signed, $hashMethod);

        return $fields;
    }

    public function url(
        Payload&Identified $request,
        ?Lang $lang = null,
        HashMethod $hashMethod = HashMethod::SHA256,
    ): string {
        return $this->config->env->paywallUrl($lang).'?'.http_build_query($this->fields($request, $hashMethod), '', '&', PHP_QUERY_RFC3986);
    }
}
