<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http;

use Pronnect\GpWebPay\DigestSigner;
use Pronnect\GpWebPay\Http\Digest\HttpRequestDigest;
use Pronnect\GpWebPay\Http\Digest\HttpResponseDigest;
use Pronnect\GpWebPay\Http\Exception\HttpRequestException;
use Pronnect\GpWebPay\Http\Exception\InvalidCallbackException;
use Pronnect\GpWebPay\Http\Exception\InvalidDigestException;
use Pronnect\GpWebPay\Http\Response\HttpResponse;
use Pronnect\GpWebPayApi\Http\HttpConfigInterface;
use Pronnect\GpWebPayApi\Http\HttpGatewayInterface;
use Pronnect\GpWebPayApi\Http\Request\HttpRequestInterface;
use Pronnect\GpWebPayApi\Http\Response\HttpResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Main GP Webpay HTTP API gateway.
 *
 * Handles request signing, redirect URL / POST form building, and callback verification.
 *
 * Usage (recommended — via factory):
 *   $gateway = HttpGateway::create($config, new DigestSigner($pubKey, $privKey, $pass));
 *
 * Usage (advanced — custom DigestSignerInterface implementation):
 *   $gateway = new HttpGateway($config, new Base64DigestSigner($myCustomSigner));
 *
 * The constructor requires Base64DigestSigner directly (not DigestSignerInterface) to
 * prevent accidental double-wrapping bugs with custom implementations.
 */
class HttpGateway implements HttpGatewayInterface
{
    /**
     * @param HttpConfigInterface $config
     * @param Base64DigestSigner  $signer  Concrete type — prevents double-wrapping bugs.
     *                                     Use create() factory when starting from a raw DigestSigner.
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        private HttpConfigInterface $config,
        private Base64DigestSigner $signer,
        private ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Recommended factory — wraps DigestSigner in Base64DigestSigner automatically.
     *
     * Use this when you have a plain DigestSigner (the common case).
     * For custom DigestSignerInterface implementations, use the constructor directly.
     */
    public static function create(
        HttpConfigInterface $config,
        DigestSigner $rawSigner,
        ?LoggerInterface $logger = null,
    ): self {
        return new self($config, new Base64DigestSigner($rawSigner), $logger);
    }

    public function getHttpUri(): string
    {
        return $this->config->getHttpUri();
    }

    /**
     * @throws HttpRequestException if ADDINFO is set (requires POST)
     */
    public function getRedirectUrl(HttpRequestInterface $request): string
    {
        if ($request->getAddInfo() !== null) {
            throw new HttpRequestException(
                'ADDINFO must not be used with GET redirect — ADDINFO requires POST form submission. '
                . 'Use getFormParams() and render a self-submitting POST form instead.',
            );
        }

        $params = $this->buildSignedParams($request);

        $this->logger->debug('HttpGateway: built GET redirect URL', [
            'ordernumber' => $params['ORDERNUMBER'] ?? null,
            'operation'   => $params['OPERATION']   ?? null,
        ]);

        return $this->config->getHttpUri() . '?' . http_build_query($params);
    }

    /**
     * @return array<string, string>
     */
    public function getFormParams(HttpRequestInterface $request): array
    {
        $params = $this->buildSignedParams($request);

        $this->logger->debug('HttpGateway: built POST form params', [
            'ordernumber' => $params['ORDERNUMBER'] ?? null,
            'operation'   => $params['OPERATION']   ?? null,
        ]);

        return $params;
    }

    /**
     * @throws InvalidCallbackException if DIGEST is missing
     * @throws InvalidDigestException   if DIGEST or DIGEST1 is invalid
     */
    public function processCallback(array $params): HttpResponseInterface
    {
        $this->verifyResponseDigest($params);

        $this->logger->debug('HttpGateway: callback verified', [
            'ordernumber' => $params['ORDERNUMBER'] ?? null,
            'prcode'      => $params['PRCODE']      ?? null,
        ]);

        return HttpResponse::fromArray($params);
    }

    /**
     * Builds signed parameter array for GET or POST.
     *
     * Flow:
     *   1. Merge MERCHANTNUMBER (from config) + request fields
     *   2. Build pipe-separated digest string
     *   3. Sign → base64 DIGEST (via Base64DigestSigner)
     *   4. Append LANG AFTER signing (LANG is NOT part of the digest)
     *
     * @return array<string, string>
     */
    private function buildSignedParams(HttpRequestInterface $request): array
    {
        // MERCHANTNUMBER must come first (it's first in the field order)
        $params = array_merge(
            ['MERCHANTNUMBER' => $this->config->getMerchantNumber()],
            $request->toArray(),
        );

        // Build digest string and sign it
        $digestString     = (new HttpRequestDigest())->build($params);
        $params['DIGEST'] = $this->signer->sign($digestString);

        // LANG is appended AFTER signing — it is not in the digest
        $lang = $request->getLang() ?? $this->config->getDefaultLang();
        if ($lang !== null && $lang !== '') {
            $params['LANG'] = $lang;
        }

        return array_map('strval', $params);
    }

    /**
     * Verifies DIGEST (mandatory) and DIGEST1 (if present) from a callback.
     *
     * @throws InvalidCallbackException if DIGEST is missing
     * @throws InvalidDigestException   if DIGEST or DIGEST1 verification fails
     */
    private function verifyResponseDigest(array $params): void
    {
        $responseDigest = new HttpResponseDigest();

        // 1. DIGEST is mandatory — every callback must include it
        if (empty($params['DIGEST'])) {
            throw new InvalidCallbackException(
                'Callback is missing required DIGEST parameter',
            );
        }

        $digestStr = $responseDigest->buildForDigest($params);
        if (!$this->signer->verify($digestStr, $params['DIGEST'])) {
            $this->logger->warning('HttpGateway: DIGEST verification failed', [
                'ordernumber' => $params['ORDERNUMBER'] ?? null,
            ]);
            throw new InvalidDigestException('DIGEST verification failed — callback rejected');
        }

        // 2. DIGEST1 — verified only if present (not guaranteed in every callback)
        // DIGEST1 includes MERCHANTNUMBER → confirms the callback belongs to this merchant
        if (!empty($params['DIGEST1'])) {
            $params['MERCHANTNUMBER'] = $this->config->getMerchantNumber();
            $digest1Str = $responseDigest->buildForDigest1($params);

            if (!$this->signer->verify($digest1Str, $params['DIGEST1'])) {
                $this->logger->warning('HttpGateway: DIGEST1 verification failed', [
                    'ordernumber' => $params['ORDERNUMBER'] ?? null,
                ]);
                throw new InvalidDigestException('DIGEST1 verification failed — callback rejected');
            }
        }
    }
}
