<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http;

use Pronnect\GpWebPayApi\DigestSignerInterface;

/**
 * Decorator that transparently handles base64 encoding/decoding of signatures
 * for the GP Webpay HTTP API.
 *
 * The HTTP API requires signatures to be base64-encoded in URL/form parameters,
 * unlike the WS (SOAP) API which uses raw binary signatures.
 *
 * Usage:
 *   $signer = new Base64DigestSigner(new DigestSigner($pubKey, $privKey, $pass));
 *   $base64Sig = $signer->sign('digest-string');      // base64-encoded
 *   $valid     = $signer->verify('digest', $base64Sig); // accepts base64 from GP Webpay
 *
 * @see HttpGateway::create() — recommended factory that wraps DigestSigner automatically
 */
class Base64DigestSigner implements DigestSignerInterface
{
    public function __construct(
        private DigestSignerInterface $inner,
    ) {}

    /**
     * Signs the digest and returns a base64-encoded signature,
     * ready for use as DIGEST parameter in HTTP API requests.
     *
     * @param string $digest Pipe-separated digest string
     * @return string Base64-encoded signature
     */
    public function sign(string $digest): string
    {
        return base64_encode($this->inner->sign($digest));
    }

    /**
     * Verifies a base64-encoded signature received from GP Webpay callbacks.
     *
     * @param string $digest    Pipe-separated digest string (reconstructed from params)
     * @param string $signature Base64-encoded signature from DIGEST or DIGEST1 parameter
     * @return bool
     */
    public function verify(string $digest, string $signature): bool
    {
        return $this->inner->verify($digest, base64_decode($signature));
    }
}
