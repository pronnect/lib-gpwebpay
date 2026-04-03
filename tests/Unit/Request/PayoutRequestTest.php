<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\PayoutRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\PayoutRequest
 */
class PayoutRequestTest extends TestCase
{
    public function testSetAndGetFields(): void
    {
        $request = new PayoutRequest();
        $request->setMasterPaymentNumber('MASTER-1')
            ->setTokenData('tok-abc')
            ->setOrderNumber('ORD-1')
            ->setReferenceNumber('REF-1')
            ->setAmount(500)
            ->setCurrencyCode(978)
            ->setPayoutType('REGULAR');

        $this->assertSame('MASTER-1', $request->getMasterPaymentNumber());
        $this->assertSame('tok-abc', $request->getTokenData());
        $this->assertSame('ORD-1', $request->getOrderNumber());
        $this->assertSame('REF-1', $request->getReferenceNumber());
        $this->assertSame(500, $request->getAmount());
        $this->assertSame(978, $request->getCurrencyCode());
        $this->assertSame('REGULAR', $request->getPayoutType());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new PayoutRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-1')
            ->setMasterPaymentNumber('MASTER-1')
            ->setTokenData('tok-abc')
            ->setOrderNumber('ORD-1')
            ->setReferenceNumber('REF-1')
            ->setAmount(500)
            ->setCurrencyCode(978)
            ->setPayoutType('REGULAR');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'PAY-1',
            'MASTER-1', 'tok-abc', 'ORD-1', 'REF-1', 500, 978, 'REGULAR',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $request = new PayoutRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-1');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'PAY-1',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new PayoutRequest())->getDigest());
    }
}