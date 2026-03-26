<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\RefundRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\RefundRequest
 */
class RefundRequestTest extends TestCase
{
    public function testSetAndGetAmount(): void
    {
        $request = new RefundRequest();
        $request->setAmount(2500);
        $this->assertSame(2500, $request->getAmount());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new RefundRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-001')
            ->setAmount(2500);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1',
            '0300',
            'merchant-001',
            'PAY-001',
            2500,
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestWithoutAmount(): void
    {
        $request = new RefundRequest();
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
        $request = new RefundRequest();
        $request->amount = 2500;
        $this->assertSame(2500, $request->amount);
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new RefundRequest())->getDigest());
    }
}
