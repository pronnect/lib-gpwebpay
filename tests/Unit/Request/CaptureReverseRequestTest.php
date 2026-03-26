<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\CaptureReverseRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\CaptureReverseRequest
 */
class CaptureReverseRequestTest extends TestCase
{
    public function testSetAndGetCaptureNumber(): void
    {
        $request = new CaptureReverseRequest();
        $request->setCaptureNumber(42);
        $this->assertSame(42, $request->getCaptureNumber());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new CaptureReverseRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-001')
            ->setCaptureNumber(42);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1',
            '0300',
            'merchant-001',
            'PAY-001',
            42,
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestWithoutCaptureNumber(): void
    {
        $request = new CaptureReverseRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-001');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1',
            '0300',
            'merchant-001',
            'PAY-001',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testMagicGetSetCaptureNumber(): void
    {
        $request = new CaptureReverseRequest();
        $request->captureNumber = 42;
        $this->assertSame(42, $request->captureNumber);
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new CaptureReverseRequest())->getDigest());
    }
}
