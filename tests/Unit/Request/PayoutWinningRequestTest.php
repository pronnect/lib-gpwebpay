<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\PayoutWinningRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\PayoutWinningRequest
 */
class PayoutWinningRequestTest extends TestCase
{
    public function testSetAndGetFields(): void
    {
        $request = new PayoutWinningRequest();
        $request->setMasterPaymentNumber('MASTER-3')
            ->setTokenData('tok-xyz')
            ->setOrderNumber('ORD-3')
            ->setReferenceNumber('REF-3')
            ->setAmount(750)
            ->setCurrencyCode(840);

        $this->assertSame('MASTER-3', $request->getMasterPaymentNumber());
        $this->assertSame('tok-xyz', $request->getTokenData());
        $this->assertSame('ORD-3', $request->getOrderNumber());
        $this->assertSame('REF-3', $request->getReferenceNumber());
        $this->assertSame(750, $request->getAmount());
        $this->assertSame(840, $request->getCurrencyCode());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new PayoutWinningRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-1')
            ->setMasterPaymentNumber('MASTER-3')
            ->setTokenData('tok-xyz')
            ->setOrderNumber('ORD-3')
            ->setReferenceNumber('REF-3')
            ->setAmount(750)
            ->setCurrencyCode(840);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'PAY-1',
            'MASTER-3', 'tok-xyz', 'ORD-3', 'REF-3', 750, 840,
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $request = new PayoutWinningRequest();
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
        $this->assertNull((new PayoutWinningRequest())->getDigest());
    }
}