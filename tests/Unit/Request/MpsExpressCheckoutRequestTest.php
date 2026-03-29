<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\MpsExpressCheckoutRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\MpsExpressCheckoutRequest
 */
class MpsExpressCheckoutRequestTest extends TestCase
{
    public function testSetAndGetFields(): void
    {
        $request = new MpsExpressCheckoutRequest();
        $request->setOrderNumber('ORD-1')
            ->setReferenceNumber('REF-1')
            ->setAmount(1500)
            ->setCurrencyCode(978)
            ->setCaptureFlag(1)
            ->setPairingNumber('PAIR-1')
            ->setCardId('CARD-1')
            ->setShippingAddressId('SHIP-1');

        $this->assertSame('ORD-1', $request->getOrderNumber());
        $this->assertSame('REF-1', $request->getReferenceNumber());
        $this->assertSame(1500, $request->getAmount());
        $this->assertSame(978, $request->getCurrencyCode());
        $this->assertSame(1, $request->getCaptureFlag());
        $this->assertSame('PAIR-1', $request->getPairingNumber());
        $this->assertSame('CARD-1', $request->getCardId());
        $this->assertSame('SHIP-1', $request->getShippingAddressId());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new MpsExpressCheckoutRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-1')
            ->setOrderNumber('ORD-1')
            ->setReferenceNumber('REF-1')
            ->setAmount(1500)
            ->setCurrencyCode(978)
            ->setCaptureFlag(1)
            ->setPairingNumber('PAIR-1')
            ->setCardId('CARD-1')
            ->setShippingAddressId('SHIP-1');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'PAY-1',
            'ORD-1', 'REF-1', 1500, 978, 1, 'PAIR-1', 'CARD-1', 'SHIP-1',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $request = new MpsExpressCheckoutRequest();
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
        $this->assertNull((new MpsExpressCheckoutRequest())->getDigest());
    }
}