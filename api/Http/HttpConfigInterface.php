<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Http;

/**
 * Configuration for the GP Webpay HTTP API client.
 *
 * @api
 */
interface HttpConfigInterface
{
    /**
     * GP Webpay HTTP API endpoint URL.
     * Test: https://test.3dsecure.gpwebpay.com/pgw/order.do
     * Prod: https://3dsecure.gpwebpay.com/pgw/order.do
     */
    public function getHttpUri(): string;

    /**
     * Merchant number from your GP Webpay contract (up to 10 digits).
     */
    public function getMerchantNumber(): string;

    /**
     * Merchant RSA private key — PEM string or file path prefixed with "file://".
     * Used for signing request DIGEST.
     */
    public function getMerchantPrivateKey(): string;

    /**
     * Password for the merchant private key, or null if the key is not protected.
     */
    public function getMerchantPrivateKeyPassword(): ?string;

    /**
     * GPE public key (bank's public key) — PEM string or file path.
     * Used for verifying response DIGEST and DIGEST1.
     */
    public function getGPEPublicKey(): string;

    /**
     * Whether the test environment is being used.
     */
    public function isTestEnvironment(): bool;

    /**
     * Default language for the payment page (ISO 639-1, e.g. "CS", "SK", "EN").
     * Sent as LANG parameter after signing — not included in the digest.
     * Returns null if no default is set.
     */
    public function getDefaultLang(): ?string;
}
