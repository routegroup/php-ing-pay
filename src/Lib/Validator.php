<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Lib;

use JsonSchema\Validator as JsonSchemaValidator;
use Routegroup\IngPay\Exceptions\InvalidSignatureException;
use Routegroup\IngPay\Exceptions\SchemaValidationException;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\HashMethod;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Types\TransactionType;

class Validator
{
    public function __construct(
        private readonly JsonSchemaValidator $jsonValidator,
        private readonly Utils $utils,
        private readonly Config $config,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data
     *
     * @throws SchemaValidationException
     */
    public function fromNotification(array $data): bool
    {
        $paymentStatuses = array_column(TransactionStatus::cases(), 'value');

        $schema = [
            'type' => 'object',
            'minProperties' => 1,
            // A notification carries at least one of the four documented
            // objects. Unknown keys stay allowed so a new one ING adds does
            // not start rejecting otherwise valid payloads.
            'anyOf' => [
                ['required' => ['transaction']],
                ['required' => ['payment']],
                ['required' => ['action']],
                ['required' => ['paymentProfile']],
            ],
            'properties' => [
                'transaction' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'string',
                        ],
                        'amount' => [
                            'type' => 'integer',
                            'minimum' => 0,
                            'exclusiveMinimum' => true,
                        ],
                        'currency' => [
                            'type' => 'string',
                            'enum' => array_column(Currency::cases(), 'value'),
                        ],
                        'status' => [
                            'type' => 'string',
                            'enum' => array_column(TransactionStatus::cases(), 'value'),
                        ],
                        'orderId' => [
                            'type' => 'string',
                        ],
                        'serviceId' => [
                            'type' => 'string',
                        ],
                        'type' => [
                            'type' => 'string',
                            'enum' => array_column(TransactionType::cases(), 'value'),
                        ],
                    ],
                    'required' => [
                        'id',
                        'amount',
                        'currency',
                        'status',
                        'orderId',
                        'serviceId',
                        'type',
                    ],
                ],
                'payment' => [
                    'type' => 'object',
                    'properties' => [
                        'status' => [
                            'type' => 'string',
                            'enum' => $paymentStatuses,
                        ],
                    ],
                ],
                'action' => [
                    'type' => 'object',
                ],
                'paymentProfile' => [
                    'type' => 'object',
                ],
            ],
        ];

        return $this->validate($data, $schema, 'notification');
    }

    /**
     * Report notifications are delivered unsigned, to their own notification
     * address, and must not be routed here: an unsigned payload is rejected.
     *
     * @throws InvalidSignatureException
     */
    public function verifySignature(string $rawBody, string $signatureHeader): void
    {
        if ($rawBody === '') {
            throw new InvalidSignatureException;
        }

        $header = $this->parseSignatureHeader($signatureHeader);

        $hashMethod = HashMethod::tryFrom($header['alg'] ?? '');

        if (! $hashMethod instanceof HashMethod) {
            throw new InvalidSignatureException;
        }

        if (! $this->utils->verifySignature($header['signature'] ?? '', $rawBody, $hashMethod)) {
            throw new InvalidSignatureException;
        }

        $this->verifyIdentifiers($header);
    }

    /** @return array<string, string> */
    private function parseSignatureHeader(string $header): array
    {
        $parsed = [];

        parse_str(str_replace(';', '&', $header), $parsed);

        return array_filter($parsed, is_string(...));
    }

    /**
     * A valid signature only proves the body was signed with our service key.
     * The header also carries the merchant and service it was issued for.
     *
     * @param  array<string, string>  $header
     *
     * @throws InvalidSignatureException
     */
    private function verifyIdentifiers(array $header): void
    {
        $expected = [
            'merchantid' => $this->config->merchantId,
            'serviceid' => $this->config->serviceId,
        ];

        foreach ($expected as $key => $value) {
            if (! isset($header[$key]) || ! hash_equals($value, $header[$key])) {
                throw new InvalidSignatureException;
            }
        }
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @param  array<array-key, mixed>  $schema
     *
     * @throws SchemaValidationException
     */
    private function validate(
        array $data,
        array $schema,
        string $schemaType
    ): bool {
        $objectData = json_decode(json_encode($data));
        $objectSchema = json_decode(json_encode($schema));

        $this->jsonValidator->validate($objectData, $objectSchema);

        if ($this->jsonValidator->isValid()) {
            return true;
        }

        $errors = [
            'schema' => $schemaType,
        ];

        foreach ($this->jsonValidator->getErrors() as $error) {
            $errors[$error['property']] = $error['message'];
        }

        throw new SchemaValidationException($errors);
    }
}
