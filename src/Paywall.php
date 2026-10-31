<?php

declare(strict_types=1);

namespace Routegroup\IngPay;

use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Types\HashMethod;
use Routegroup\IngPay\Types\Lang;
use Routegroup\IngPay\Value\Cart;
use Routegroup\IngPay\Value\Invoice;

/**
 * The gateway's browser interface, the peer of Api. The paywall is a signed
 * form the payer's browser submits, not an endpoint, so this builds the form
 * rather than sending anything.
 */
class Paywall
{
    public function __construct(
        private readonly Config $config,
        private readonly Utils $utils,
    ) {}

    /**
     * The signed fields are the unit: a url is one rendering of them, the card
     * widget's data-* attributes are another.
     *
     * @return array<string, mixed>
     */
    public function fields(Payload&Identified $request, HashMethod $hashMethod = HashMethod::SHA256): array
    {
        $fields = $request->withIdentity($this->config)->toArray();

        // The paywall takes an invoice and a cart encoded, not expanded: that is
        // a property of this channel, so the request keeps holding real values.
        if (($request->invoice ?? null) instanceof Invoice) {
            $fields['invoice'] = $request->invoice->encoded();
        }

        if (($request->cart ?? null) instanceof Cart) {
            $fields['cart'] = $request->cart->encoded();
        }

        unset($fields['signature']);

        // The widget adds presentation fields after signing, so signing them
        // here would produce a digest the gateway cannot reproduce.
        $signed = $request instanceof HasUnsignedFields
            ? array_diff_key($fields, array_flip($request->unsignedFields()))
            : $fields;

        $fields['signature'] = $this->utils->createSignature($signed, $hashMethod);

        return $fields;
    }

    /**
     * A url rather than a redirect, so an integration can render it however it
     * likes — a redirect, a form, a QR code, an e-mail.
     */
    public function url(
        Payload&Identified $request,
        ?Lang $lang = null,
        HashMethod $hashMethod = HashMethod::SHA256,
    ): string {
        return $this->config->env->paywallUrl($lang).'?'.http_build_query($this->fields($request, $hashMethod), '', '&', PHP_QUERY_RFC3986);
    }
}
