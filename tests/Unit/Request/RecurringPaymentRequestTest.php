<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\AltTerminalData;
use Pronnect\GpWebPay\Request\CardHolderData;
use Pronnect\GpWebPay\Request\RecurringPaymentRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\RecurringPaymentRequest
 */
class RecurringPaymentRequestTest extends TestCase
{
    public function testSetAndGetFields(): void
    {
        $request = new RecurringPaymentRequest();
        $request->setMasterPaymentNumber('MASTER-1')
            ->setOrderNumber('ORD-1')
            ->setReferenceNumber('REF-1')
            ->setAmount(1000)
            ->setCurrencyCode(978)
            ->setCaptureFlag(1);

        $this->assertSame('MASTER-1', $request->getMasterPaymentNumber());
        $this->assertSame('ORD-1', $request->getOrderNumber());
        $this->assertSame('REF-1', $request->getReferenceNumber());
        $this->assertSame(1000, $request->getAmount());
        $this->assertSame(978, $request->getCurrencyCode());
        $this->assertSame(1, $request->getCaptureFlag());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new RecurringPaymentRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-1')
            ->setMasterPaymentNumber('MASTER-1')
            ->setOrderNumber('ORD-1')
            ->setReferenceNumber('REF-1')
            ->setAmount(1000)
            ->setCurrencyCode(978)
            ->setCaptureFlag(1);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'PAY-1',
            'MASTER-1', 'ORD-1', 'REF-1', 1000, 978, 1,
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $request = new RecurringPaymentRequest();
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
        $this->assertNull((new RecurringPaymentRequest())->getDigest());
    }

    public function testOptionalComplexFieldSettersAndGetters(): void
    {
        $request     = new RecurringPaymentRequest();
        $cardHolder  = new CardHolderData();
        $altTerminal = new AltTerminalData();

        $request->setCardHolderData($cardHolder)->setAltTerminalData($altTerminal);

        $this->assertSame($cardHolder, $request->getCardHolderData());
        $this->assertSame($altTerminal, $request->getAltTerminalData());
    }
}