<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\DigestSigner;
use Pronnect\GpWebPay\Http\Base64DigestSigner;
use Pronnect\GpWebPay\Http\Digest\HttpResponseDigest;
use Pronnect\GpWebPay\ReturnUrlVerifier;

/**
 * @covers \Pronnect\GpWebPay\ReturnUrlVerifier
 * @uses   \Pronnect\GpWebPay\DigestSigner
 * @uses   \Pronnect\GpWebPay\Http\Base64DigestSigner
 * @uses   \Pronnect\GpWebPay\Http\Digest\HttpResponseDigest
 */
class ReturnUrlVerifierTest extends TestCase
{
    /** GPE public key — used by merchant to verify GP Webpay's signatures */
    private string $gpePublicKey;
    /** GPE private key — used in tests to simulate GP Webpay signing */
    private string $gpePrivateKey;
    private string $merchantNumber;

    protected function setUp(): void
    {
        // GP Webpay signs callbacks with the GPE private key.
        // Merchants verify with the GPE public key.
        $this->gpePublicKey  = file_get_contents(getenv('GPWEBPAY_PUBLIC_KEY'));
        $this->gpePrivateKey = file_get_contents(getenv('GPWEBPAY_PRIVATE_KEY'));
        $this->merchantNumber = '0123456789';
    }

    /**
     * Build a valid callback signed with the GPE private key
     * (simulating how GP Webpay actually signs callbacks).
     */
    private function buildValidCallbackParams(
        array $extraParams = [],
        string $merchantNumber = '0123456789',
    ): array {
        $params = array_merge([
            'OPERATION'   => 'CREATE_ORDER',
            'ORDERNUMBER' => '123456',
            'PRCODE'      => '0',
            'SRCODE'      => '0',
            'RESULTTEXT'  => 'OK',
        ], $extraParams);

        $responseDigest = new HttpResponseDigest();

        // Sign with GPE private key — this is how GP Webpay creates the callback signatures
        $signer = new Base64DigestSigner(
            new DigestSigner(
                $this->gpePublicKey,  // public key for verification
                $this->gpePrivateKey, // private key for signing (simulating GP Webpay)
            ),
        );

        $digestStr        = $responseDigest->buildForDigest($params);
        $params['DIGEST'] = $signer->sign($digestStr);

        $paramsWithMerchant  = array_merge($params, ['MERCHANTNUMBER' => $merchantNumber]);
        $digest1Str          = $responseDigest->buildForDigest1($paramsWithMerchant);
        $params['DIGEST1']   = $signer->sign($digest1Str);

        return $params;
    }

    public function testReturnsFalseWhenDigest1IsMissing(): void
    {
        $verifier = new ReturnUrlVerifier($this->gpePublicKey, $this->merchantNumber);

        $params = [
            'OPERATION'   => 'CREATE_ORDER',
            'ORDERNUMBER' => '123456',
            'PRCODE'      => '0',
            'SRCODE'      => '0',
            'RESULTTEXT'  => 'OK',
            // No DIGEST1
        ];

        $this->assertFalse($verifier->verify($params));
    }

    public function testReturnsFalseWhenDigest1IsEmpty(): void
    {
        $verifier = new ReturnUrlVerifier($this->gpePublicKey, $this->merchantNumber);

        $params = [
            'OPERATION'   => 'CREATE_ORDER',
            'ORDERNUMBER' => '123456',
            'PRCODE'      => '0',
            'SRCODE'      => '0',
            'RESULTTEXT'  => 'OK',
            'DIGEST1'     => '',
        ];

        $this->assertFalse($verifier->verify($params));
    }

    public function testReturnsFalseWhenDigest1IsInvalid(): void
    {
        $verifier = new ReturnUrlVerifier($this->gpePublicKey, $this->merchantNumber);

        $params = [
            'OPERATION'   => 'CREATE_ORDER',
            'ORDERNUMBER' => '123456',
            'PRCODE'      => '0',
            'SRCODE'      => '0',
            'RESULTTEXT'  => 'OK',
            'DIGEST1'     => base64_encode('invalid-signature-bytes'),
        ];

        $this->assertFalse($verifier->verify($params));
    }

    public function testReturnsFalseWhenDigestIsPresentButInvalid(): void
    {
        $verifier = new ReturnUrlVerifier($this->gpePublicKey, $this->merchantNumber);

        $validParams = $this->buildValidCallbackParams();
        // Tamper DIGEST — DIGEST1 is still valid but DIGEST should fail
        $validParams['DIGEST'] = base64_encode('tampered-digest');

        $this->assertFalse($verifier->verify($validParams));
    }

    public function testReturnsTrueWithValidDigest1(): void
    {
        $verifier = new ReturnUrlVerifier($this->gpePublicKey, $this->merchantNumber);
        $params   = $this->buildValidCallbackParams();

        $this->assertTrue($verifier->verify($params));
    }

    public function testUsesExpandedDigest1FieldOrderWith19Fields(): void
    {
        // Verifies that optional fields (MD, TOKEN, EXPIRY, etc.) are included
        // in the DIGEST1 — tests the full 19+1 field order per spec kap. 5.2
        $extraParams = [
            'MD'         => 'myref',
            'TOKEN'      => 'tok-abc-123',
            'EXPIRY'     => '2612',
            'ACSRES'     => 'Y',
            'PANPATTERN' => '405607*******0016',
        ];

        $params = $this->buildValidCallbackParams($extraParams);

        $verifier = new ReturnUrlVerifier($this->gpePublicKey, $this->merchantNumber);
        $this->assertTrue($verifier->verify($params));
    }

    public function testReturnsFalseWhenMerchantNumberDoesNotMatchDigest1(): void
    {
        // Build valid params for merchant '0123456789'
        $params = $this->buildValidCallbackParams([], '0123456789');

        // Verify with a different merchant number — DIGEST1 must fail
        $verifier = new ReturnUrlVerifier($this->gpePublicKey, 'WRONG_MERCHANT');

        $this->assertFalse($verifier->verify($params));
    }

    public function testVerifyReturnsTrueWhenDigestIsMissingButDigest1IsValid(): void
    {
        // ReturnUrlVerifier: DIGEST is optional, DIGEST1 is required
        $params = $this->buildValidCallbackParams();
        unset($params['DIGEST']); // Remove DIGEST — should still pass

        $verifier = new ReturnUrlVerifier($this->gpePublicKey, $this->merchantNumber);

        $this->assertTrue($verifier->verify($params));
    }
}
