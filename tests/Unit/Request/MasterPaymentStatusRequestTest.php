<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\MasterPaymentStatusRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\MasterPaymentStatusRequest
 */
class MasterPaymentStatusRequestTest extends TestCase
{
    public function testGetDigestWithAllFields(): void
    {
        $request = new MasterPaymentStatusRequest();
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

    public function testGetDigestWithoutPaymentNumber(): void
    {
        $request = new MasterPaymentStatusRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1',
            '0300',
            'merchant-001',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new MasterPaymentStatusRequest())->getDigest());
    }
}
