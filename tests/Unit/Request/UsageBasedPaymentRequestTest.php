<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\AltTerminalData;
use Pronnect\GpWebPay\Request\CardHolderData;
use Pronnect\GpWebPay\Request\PaymentInfo;
use Pronnect\GpWebPay\Request\ShoppingCartInfo;
use Pronnect\GpWebPay\Request\SubMerchantData;
use Pronnect\GpWebPay\Request\UsageBasedPaymentRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\UsageBasedPaymentRequest
 */
class UsageBasedPaymentRequestTest extends TestCase
{
    public function testSetAndGetFields(): void
    {
        $request = new UsageBasedPaymentRequest();
        $request->setPaymentNumber('PAY-1')
            ->setAmount(2000)
            ->setCurrencyCode(840)
            ->setCaptureFlag(0)
            ->setTokenData('token-abc')
            ->setReturnUrl('https://example.com/return')
            ->setOrderNumber('ORD-1')
            ->setReferenceNumber('REF-1');

        $this->assertSame('PAY-1', $request->getPaymentNumber());
        $this->assertSame(2000, $request->getAmount());
        $this->assertSame(840, $request->getCurrencyCode());
        $this->assertSame(0, $request->getCaptureFlag());
        $this->assertSame('token-abc', $request->getTokenData());
        $this->assertSame('https://example.com/return', $request->getReturnUrl());
        $this->assertSame('ORD-1', $request->getOrderNumber());
        $this->assertSame('REF-1', $request->getReferenceNumber());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new UsageBasedPaymentRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-1')
            ->setOrderNumber('ORD-1')
            ->setReferenceNumber('REF-1')
            ->setAmount(2000)
            ->setCurrencyCode(840)
            ->setCaptureFlag(0)
            ->setTokenData('token-abc')
            ->setReturnUrl('https://example.com/return');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'PAY-1',
            'ORD-1', 'REF-1', 2000, 840, 0, 'token-abc', 'https://example.com/return',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $request = new UsageBasedPaymentRequest();
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
        $this->assertNull((new UsageBasedPaymentRequest())->getDigest());
    }

    public function testOptionalComplexFieldSettersAndGetters(): void
    {
        $request = new UsageBasedPaymentRequest();
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