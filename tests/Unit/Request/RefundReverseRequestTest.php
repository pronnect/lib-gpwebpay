<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\RefundReverseRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\RefundReverseRequest
 */
class RefundReverseRequestTest extends TestCase
{
    public function testSetAndGetRefundNumber(): void
    {
        $request = new RefundReverseRequest();
        $request->setRefundNumber(7);
        $this->assertSame(7, $request->getRefundNumber());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new RefundReverseRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-001')
            ->setRefundNumber(7);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1',
            '0300',
            'merchant-001',
            'PAY-001',
            7,
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestWithoutRefundNumber(): void
    {
        $request = new RefundReverseRequest();
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

    public function testMagicGetSetRefundNumber(): void
    {
        $request = new RefundReverseRequest();
        $request->refundNumber = 7;
        $this->assertSame(7, $request->refundNumber);
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new RefundReverseRequest())->getDigest());
    }
}
