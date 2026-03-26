<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay;

use Pronnect\GpWebPay\Http\Base64DigestSigner;
use Pronnect\GpWebPay\Http\Digest\HttpResponseDigest;
use Pronnect\GpWebPayApi\ReturnUrlVerifierInterface;

/**
 * Verifies the DIGEST and DIGEST1 signatures on GP Webpay HTTP return-URL callbacks.
 *
 * Field order is delegated to HttpResponseDigest — the single source of truth
 * shared with HttpGateway::processCallback().
 *
 * Usage:
 *   $verifier = new ReturnUrlVerifier($gpePublicKey, $merchantNumber);
 *   if (!$verifier->verify($_GET)) {
 *       // reject — signature invalid
 *   }
 */
class ReturnUrlVerifier implements ReturnUrlVerifierInterface
{
    private Base64DigestSigner $signer;
    private string $merchantNumber;

    /**
     * @param string      $gpePublicKey              GPE public key (PEM string or file path).
     * @param string      $merchantNumber             Merchant number from your GP Webpay contract.
     * @param string|null $merchantPrivateKey         Only required if you also need to verify DIGEST
     *                                                (merchant-signed); pass null to skip DIGEST check.
     * @param string|null $merchantPrivateKeyPassword Private key password (if protected).
     */
    public function __construct(
        string $gpePublicKey,
        string $merchantNumber,
        ?string $merchantPrivateKey = null,
        ?string $merchantPrivateKeyPassword = null,
    ) {
        $this->merchantNumber = $merchantNumber;
        $this->signer = new Base64DigestSigner(
            new DigestSigner(
                $gpePublicKey,
                $merchantPrivateKey ?? $gpePublicKey, // fallback — verify() uses only public key
                $merchantPrivateKeyPassword,
            ),
        );
    }

    /**
     * Verify DIGEST (optional) and DIGEST1 (required) signatures.
     *
     * - DIGEST  is verified if present; failure → false
     * - DIGEST1 is required; missing or invalid → false
     *
     * @param array<string,string|null> $params  Raw $_GET or $_POST callback parameters.
     */
    public function verify(array $params): bool
    {
        $responseDigest = new HttpResponseDigest();

        // 1. Verify DIGEST if present
        if (!empty($params['DIGEST'])) {
            $digestStr = $responseDigest->buildForDigest($params);
            if (!$this->signer->verify($digestStr, $params['DIGEST'])) {
                return false;
            }
        }

        // 2. DIGEST1 is required for return-URL verification
        if (empty($params['DIGEST1'])) {
            return false;
        }

        // MERCHANTNUMBER is not in the callback — merchant adds it from config
        $params['MERCHANTNUMBER'] = $this->merchantNumber;
        $digest1Str = $responseDigest->buildForDigest1($params);

        return $this->signer->verify($digest1Str, $params['DIGEST1']);
    }
}
