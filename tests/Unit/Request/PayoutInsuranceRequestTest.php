<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\PayoutInsuranceRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\PayoutInsuranceRequest
 */
class PayoutInsuranceRequestTest extends TestCase
{
    public function testSetAndGetFields(): void
    {
        $request = new PayoutInsuranceRequest();
        $request->setMasterPaymentNumber('MASTER-4')
            ->setTokenData('tok-ins')
            ->setOrderNumber('ORD-4')
            ->setReferenceNumber('REF-4')
            ->setAmount(250)
            ->setCurrencyCode(978);

        $this->assertSame('MASTER-4', $request->getMasterPaymentNumber());
        $this->assertSame('tok-ins', $request->getTokenData());
        $this->assertSame('ORD-4', $request->getOrderNumber());
        $this->assertSame('REF-4', $request->getReferenceNumber());
        $this->assertSame(250, $request->getAmount());
        $this->assertSame(978, $request->getCurrencyCode());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new PayoutInsuranceRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-1')
            ->setMasterPaymentNumber('MASTER-4')
            ->setTokenData('tok-ins')
            ->setOrderNumber('ORD-4')
            ->setReferenceNumber('REF-4')
            ->setAmount(250)
            ->setCurrencyCode(978);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'PAY-1',
            'MASTER-4', 'tok-ins', 'ORD-4', 'REF-4', 250, 978,
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $request = new PayoutInsuranceRequest();
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
        $this->assertNull((new PayoutInsuranceRequest())->getDigest());
    }
}