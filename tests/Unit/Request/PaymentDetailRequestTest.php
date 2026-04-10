<?php

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\PaymentDetailRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * Class PaymentDetailRequestTest
 * @covers \Pronnect\GpWebPay\Request\PaymentDetailRequest
 */
class PaymentDetailRequestTest extends TestCase
{
    public function testGetDigestWithValidData(): void
    {
        $request = new PaymentDetailRequest();
        $request->setMessageId('12345')
            ->setProvider('provider')
            ->setMerchantNumber('67890')
            ->setPaymentNumber('54321');

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            '12345',
            'provider',
            '67890',
            '54321',
        ]);
        $this->assertSame($expectedDigest, $request->getDigest());
    }

    /**
     * @return void
     */
    public function testSetAndGetMessageId(): void
    {
        $request = new PaymentDetailRequest();
        $request->setMessageId('12345');
        $this->assertSame('12345', $request->getMessageId());
    }

    /**
     * @return void
     */
    public function testSetAndGetProvider(): void
    {
        $request = new PaymentDetailRequest();
        $request->setProvider('provider');
        $this->assertSame('provider', $request->getProvider());
    }

    /**
     * @return void
     */
    public function testSetAndGetMerchantNumber(): void
    {
        $request = new PaymentDetailRequest();
        $request->setMerchantNumber('67890');
        $this->assertSame('67890', $request->getMerchantNumber());
    }

    /**
     * @return void
     */
    public function testSetAndGetPaymentNumber(): void
    {
        $request = new PaymentDetailRequest();
        $request->setPaymentNumber('54321');
        $this->assertSame('54321', $request->getPaymentNumber());
    }
}
