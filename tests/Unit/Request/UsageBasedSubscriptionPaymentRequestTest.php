<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\AltTerminalData;
use Pronnect\GpWebPay\Request\CardHolderData;
use Pronnect\GpWebPay\Request\PaymentInfo;
use Pronnect\GpWebPay\Request\ShoppingCartInfo;
use Pronnect\GpWebPay\Request\SubMerchantData;
use Pronnect\GpWebPay\Request\UsageBasedSubscriptionPaymentRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\UsageBasedSubscriptionPaymentRequest
 */
class UsageBasedSubscriptionPaymentRequestTest extends TestCase
{
    public function testSetAndGetFields(): void
    {
        $request = new UsageBasedSubscriptionPaymentRequest();
        $request->setMasterPaymentNumber('MASTER-1')
            ->setOrderNumber('ORD-1')
            ->setReferenceNumber('REF-1')
            ->setAmount(500)
            ->setCurrencyCode(978)
            ->setCaptureFlag(1);

        $this->assertSame('MASTER-1', $request->getMasterPaymentNumber());
        $this->assertSame('ORD-1', $request->getOrderNumber());
        $this->assertSame('REF-1', $request->getReferenceNumber());
        $this->assertSame(500, $request->getAmount());
        $this->assertSame(978, $request->getCurrencyCode());
        $this->assertSame(1, $request->getCaptureFlag());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new UsageBasedSubscriptionPaymentRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-1')
            ->setMasterPaymentNumber('MASTER-1')
            ->setOrderNumber('ORD-1')
            ->setReferenceNumber('REF-1')
            ->setAmount(500)
            ->setCurrencyCode(978)
            ->setCaptureFlag(1);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'PAY-1',
            'MASTER-1', 'ORD-1', 'REF-1', 500, 978, 1,
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $request = new UsageBasedSubscriptionPaymentRequest();
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
        $this->assertNull((new UsageBasedSubscriptionPaymentRequest())->getDigest());
    }

    public function testOptionalComplexFieldSettersAndGetters(): void
    {
        $request     = new UsageBasedSubscriptionPaymentRequest();
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