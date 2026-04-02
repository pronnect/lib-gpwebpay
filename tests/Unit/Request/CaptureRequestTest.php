<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\CaptureRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\CaptureRequest
 */
class CaptureRequestTest extends TestCase
{
    public function testSetAndGetAmount(): void
    {
        $request = new CaptureRequest();
        $request->setAmount(5000);
        $this->assertSame(5000, $request->getAmount());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new CaptureRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-001')
            ->setAmount(5000);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1',
            '0300',
            'merchant-001',
            'PAY-001',
            5000,
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestWithoutAmount(): void
    {
        $request = new CaptureRequest();
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

    public function testMagicGetSetAmount(): void
    {
        $request = new CaptureRequest();
        $request->amount = 5000;
        $this->assertSame(5000, $request->amount);
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new CaptureRequest())->getDigest());
    }
}
