<?php

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\CardDataRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\CardDataRequest
 */
class CardDataRequestTest extends TestCase
{
    public function testSetAndGetTokenData(): void
    {
        $request = new CardDataRequest();
        $request->setTokenData('tok-data-001');
        $this->assertSame('tok-data-001', $request->getTokenData());
    }

    public function testSetAndGetMasterPaymentNumber(): void
    {
        $request = new CardDataRequest();
        $request->setMasterPaymentNumber('MASTER-001');
        $this->assertSame('MASTER-001', $request->getMasterPaymentNumber());
    }

    public function testGetDigestWithTokenData(): void
    {
        $request = new CardDataRequest();
        $request->setMessageId('msg-4')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setTokenData('tok-data-001');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-4',
            '0300',
            'merchant-001',
            'tok-data-001',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestWithMasterPaymentNumber(): void
    {
        $request = new CardDataRequest();
        $request->setMessageId('msg-4')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setMasterPaymentNumber('MASTER-001');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-4',
            '0300',
            'merchant-001',
            'MASTER-001',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }
}
