<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Http;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\DigestSigner;
use Pronnect\GpWebPay\Http\Base64DigestSigner;
use Pronnect\GpWebPayApi\DigestSignerInterface;

/**
 * @covers \Pronnect\GpWebPay\Http\Base64DigestSigner
 * @uses   \Pronnect\GpWebPay\DigestSigner
 */
class Base64DigestSignerTest extends TestCase
{
    private Base64DigestSigner $signer;

    protected function setUp(): void
    {
        $inner = new DigestSigner(
            file_get_contents(getenv('GPWEBPAY_PUBLIC_KEY')),
            file_get_contents(getenv('GPWEBPAY_MERCHANT_PRIVATE_KEY')),
            getenv('GPWEBPAY_MERCHANT_PRIVATE_KEY_PASSWORD') ?: null,
        );
        $this->signer = new Base64DigestSigner($inner);
    }

    public function testImplementsDigestSignerInterface(): void
    {
        $this->assertInstanceOf(DigestSignerInterface::class, $this->signer);
    }

    public function testSignReturnsValidBase64(): void
    {
        $signature = $this->signer->sign('test-digest');

        $this->assertNotEmpty($signature);
        // Valid base64 contains only A-Z, a-z, 0-9, +, /, = characters
        $this->assertMatchesRegularExpression(
            '/^[A-Za-z0-9+\/]+=*$/',
            $signature,
            'sign() must return a valid base64-encoded string',
        );
    }

    public function testSignReturnsDifferentValueThanRawBinary(): void
    {
        $digest    = 'test-digest-string';
        $signature = $this->signer->sign($digest);

        // base64 string should be printable ASCII — raw binary would not be
        $this->assertTrue(ctype_print($signature), 'Base64 output must be printable ASCII');
    }

    public function testVerifyAcceptsBase64EncodedSignature(): void
    {
        $digest    = 'merchant|CREATE_ORDER|123456|19900|203|1';
        $signature = $this->signer->sign($digest);

        // verify() should accept the base64 signature produced by sign()
        // Note: cross-key verification will fail (signed with merchant key, verified with GPE key),
        // so we use a mock to isolate the base64 decoding behavior
        $mockInner = $this->createMock(DigestSignerInterface::class);
        $mockInner
            ->expects($this->once())
            ->method('verify')
            ->with(
                $this->equalTo($digest),
                $this->callback(fn($sig) => !ctype_print($sig)), // raw binary is not printable
            )
            ->willReturn(true);

        // Use actual non-printable binary bytes — ctype_print() must return false for raw binary
        $rawBinary = "\x00\x01\xDE\xAD\xBE\xEF\xFF\xFE";

        $signer = new Base64DigestSigner($mockInner);
        $result = $signer->verify($digest, base64_encode($rawBinary));

        $this->assertTrue($result);
    }

    public function testVerifyDecodesBase64BeforePassingToInner(): void
    {
        $digest       = 'test-digest';
        $rawBinary    = 'raw-binary-signature-data';
        $base64Sig    = base64_encode($rawBinary);

        $mockInner = $this->createMock(DigestSignerInterface::class);
        $mockInner
            ->expects($this->once())
            ->method('verify')
            ->with($digest, $rawBinary)  // must receive raw binary, not base64
            ->willReturn(true);

        $signer = new Base64DigestSigner($mockInner);
        $this->assertTrue($signer->verify($digest, $base64Sig));
    }

    public function testVerifyReturnsFalseForWrongSignature(): void
    {
        $mockInner = $this->createMock(DigestSignerInterface::class);
        $mockInner->method('verify')->willReturn(false);

        $signer = new Base64DigestSigner($mockInner);
        $this->assertFalse($signer->verify('digest', base64_encode('wrong-signature')));
    }

    public function testRoundTripWithMockedInner(): void
    {
        $digest    = 'merchantNumber|CREATE_ORDER|12345|19900|203|1';
        $rawBinary = 'raw-binary-sig-from-openssl';

        $mockInner = $this->createMock(DigestSignerInterface::class);
        $mockInner->method('sign')->willReturn($rawBinary);
        $mockInner->method('verify')->willReturn(true);

        $signer    = new Base64DigestSigner($mockInner);
        $signature = $signer->sign($digest);

        // Signature must be valid base64
        $this->assertSame(base64_encode($rawBinary), $signature);

        // verify() must decode and pass back to inner
        $mockInner2 = $this->createMock(DigestSignerInterface::class);
        $mockInner2
            ->expects($this->once())
            ->method('verify')
            ->with($digest, $rawBinary)
            ->willReturn(true);

        $signer2 = new Base64DigestSigner($mockInner2);
        $this->assertTrue($signer2->verify($digest, $signature));
    }
}
