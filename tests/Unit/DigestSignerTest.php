<?php

namespace Pronnect\GpWebPayTest\Unit;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\DigestSigner;
use Pronnect\GpWebPayApi\Response\ResponseInterface;
use Pronnect\GpWebPayApi\SignedInterface;
use RuntimeException;

/**
 * Class DigestSignerTest
 * @covers \Pronnect\GpWebPay\DigestSigner
 */
class DigestSignerTest extends TestCase
{
    private string $signaturePrivateKey;
    private string $signaturePublicKey;
    private string $privateKey;
    private ?string $privateKeyPassword;

    protected function setUp(): void
    {
        $this->signaturePrivateKey = file_get_contents(getenv('GPWEBPAY_PRIVATE_KEY'));
        $this->signaturePublicKey  = file_get_contents(getenv('GPWEBPAY_PUBLIC_KEY'));
        $this->privateKey          = file_get_contents(getenv('GPWEBPAY_MERCHANT_PRIVATE_KEY'));
        $this->privateKeyPassword  = getenv('GPWEBPAY_MERCHANT_PRIVATE_KEY_PASSWORD') ?: null;
    }

    public function testSign(): void
    {
        $digestSigner = new DigestSigner($this->signaturePublicKey, $this->privateKey, $this->privateKeyPassword);
        $digest = 'test-digest';
        $signature = $digestSigner->sign($digest);

        $this->assertNotEmpty($signature);
    }

    public function testVerify(): void
    {
        $digestSigner = new DigestSigner($this->signaturePublicKey, $this->privateKey, $this->privateKeyPassword);
        $digest = 'test-digest';
        $signature = $digestSigner->sign($digest);

        $this->assertFalse($digestSigner->verify($digest, $signature));
    }

    public function testSignWrongPassword(): void
    {
        $this->expectException(RuntimeException::class);
        $digestSigner = new DigestSigner($this->signaturePublicKey, $this->privateKey, "wrongPassword");
        $digest = 'test-digest';
        $digestSigner->sign($digest);
    }
}
