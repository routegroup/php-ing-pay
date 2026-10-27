<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Lib;

use BackedEnum;
use Routegroup\IngPay\Payload;
use Routegroup\IngPay\Types\HashMethod;

class Utils
{
    public function __construct(private readonly Config $config) {}

    /** @param array<array-key, mixed> $orderData */
    public function createSignature(array $orderData, HashMethod $hashMethod = HashMethod::SHA256): string
    {
        $hash = hash($hashMethod->value, $this->buildQuery($orderData).$this->config->serviceKey);

        return "{$hash};{$hashMethod->value}";
    }

    public function verifySignature(string $signature, string $rawBody, HashMethod $hashMethod): bool
    {
        return hash_equals(hash($hashMethod->value, $rawBody.$this->config->serviceKey), $signature);
    }

    /** @param array<array-key, mixed> $orderData */
    public function buildQuery(array $orderData): string
    {
        return $this->prepareData($orderData);
    }

    /**
     * Headers that look like the gateway's, for testing a notification endpoint
     * without the gateway.
     *
     * @return array<string, string>
     */
    public function mockHeaders(Payload $payload, HashMethod $hashMethod = HashMethod::SHA256): array
    {
        $body = json_encode($payload->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $signature = hash($hashMethod->value, $body.$this->config->serviceKey);

        return ['x-imoje-signature' => implode(';', [
            "merchantid={$this->config->merchantId}",
            "serviceid={$this->config->serviceId}",
            "signature={$signature}",
            "alg={$hashMethod->value}",
        ])];
    }

    /**
     * Mirrors the reference prepareData() published in the paywall
     * documentation: sort by key at every level, write a nested key as
     * parent[child], join the pairs with '&'.
     *
     * A null is skipped rather than emitted as "key=", because the request
     * itself drops nulls — emitting one here would sign a parameter the gateway
     * never receives.
     *
     * @param  array<array-key, mixed>  $data
     */
    private function prepareData(array $data, string $prefix = ''): string
    {
        $data = $this->transformValues($data);

        ksort($data);

        $pairs = [];

        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }

            $key = $prefix === '' ? (string) $key : $prefix.'['.$key.']';

            $pair = is_array($value)
                ? $this->prepareData($value, $key)
                : $key.'='.$value;

            if ($pair !== '') {
                $pairs[] = $pair;
            }
        }

        return implode('&', $pairs);
    }

    /**
     * @param  array<array-key, mixed>  $values
     * @return array<array-key, mixed>
     */
    public function transformValues(array $values): array
    {
        return array_map(
            static fn (mixed $value): mixed => match (true) {
                $value instanceof Payload => $value->toArray(),
                $value instanceof BackedEnum => $value->value,
                default => $value,
            },
            $values,
        );
    }
}
