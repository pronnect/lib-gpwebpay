<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\AltTerminalData;
use Pronnect\GpWebPay\Request\CardHolderData;
use Pronnect\GpWebPay\Request\PaymentInfo;
use Pronnect\GpWebPay\Request\PrepaidPaymentRequest;
use Pronnect\GpWebPay\Request\ShoppingCartInfo;
use Pronnect\GpWebPay\Request\SubMerchantData;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\PrepaidPaymentRequest
 */
class PrepaidPaymentRequestTest extends TestCase
{
    public function testSetAndGetFields(): void
    {
        $request = new PrepaidPaymentRequest();
        $request->setMasterPaymentNumber('MASTER-2')
            ->setOrderNumber('ORD-2')
            ->setReferenceNumber('REF-2')
            ->setSubscriptionAmount(3000)
            ->setCaptureFlag(0);

        $this->assertSame('MASTER-2', $request->getMasterPaymentNumber());
        $this->assertSame('ORD-2', $request->getOrderNumber());
        $this->assertSame('REF-2', $request->getReferenceNumber());
        $this->assertSame(3000, $request->getSubscriptionAmount());
        $this->assertSame(0, $request->getCaptureFlag());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new PrepaidPaymentRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-1')
            ->setMasterPaymentNumber('MASTER-2')
            ->setOrderNumber('ORD-2')
            ->setReferenceNumber('REF-2')
            ->setSubscriptionAmount(3000)
            ->setCaptureFlag(0);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'PAY-1',
            'MASTER-2', 'ORD-2', 'REF-2', 3000, 0,
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $request = new PrepaidPaymentRequest();
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
        $this->assertNull((new PrepaidPaymentRequest())->getDigest());
    }

    public function testOptionalComplexFieldSettersAndGetters(): void
    {
        $request     = new PrepaidPaymentRequest();
        $subMerchant = new SubMerchantData();
        $cardHolder  = new CardHolderData();
        $paymentInfo = new PaymentInfo();
        $cartInfo    = new ShoppingCartInfo();
        $altTerminal = new AltTerminalData();

        $request->setSubMerchantData($subMerchant)
            ->setCardHolderData($cardHolder)
            ->setPaymentInfo($paymentInfo)
            ->setShoppingCartInfo($cartInfo)
            ->setAltTerminalData($altTerminal);

        $this->assertSame($subMerchant, $request->getSubMerchantData());
        $this->assertSame($cardHolder, $request->getCardHolderData());
        $this->assertSame($paymentInfo, $request->getPaymentInfo());
        $this->assertSame($cartInfo, $request->getShoppingCartInfo());
        $this->assertSame($altTerminal, $request->getAltTerminalData());
    }
}