<?php

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\AltTerminalData;
use Pronnect\GpWebPay\Request\CardHolderData;
use Pronnect\GpWebPay\Request\CardOnFilePaymentRequest;
use Pronnect\GpWebPay\Request\PaymentInfo;
use Pronnect\GpWebPay\Request\ShoppingCartInfo;
use Pronnect\GpWebPay\Request\SubMerchantData;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\CardOnFilePaymentRequest
 * @uses \Pronnect\GpWebPay\Request\AltTerminalData
 * @uses \Pronnect\GpWebPay\Request\CardHolderData
 * @uses \Pronnect\GpWebPay\Request\PaymentInfo
 * @uses \Pronnect\GpWebPay\Request\ShoppingCartInfo
 * @uses \Pronnect\GpWebPay\Request\SubMerchantData
 */
class CardOnFilePaymentRequestTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $request = new CardOnFilePaymentRequest();
        $request->setPaymentNumber('PAY-001')
            ->setAmount(5000)
            ->setCurrencyCode(978)
            ->setCaptureFlag(1)
            ->setTokenData('tok-data')
            ->setReturnUrl('https://example.com/return')
            ->setOrderNumber('ORD-123')
            ->setReferenceNumber('REF-456');

        $this->assertSame('PAY-001', $request->getPaymentNumber());
        $this->assertSame(5000, $request->getAmount());
        $this->assertSame(978, $request->getCurrencyCode());
        $this->assertSame(1, $request->getCaptureFlag());
        $this->assertSame('tok-data', $request->getTokenData());
        $this->assertSame('https://example.com/return', $request->getReturnUrl());
        $this->assertSame('ORD-123', $request->getOrderNumber());
        $this->assertSame('REF-456', $request->getReferenceNumber());
    }

    public function testGetDigestWithRequiredAndOptionalFields(): void
    {
        $request = new CardOnFilePaymentRequest();
        $request->setMessageId('msg-3')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-001')
            ->setOrderNumber('ORD-123')
            ->setReferenceNumber('REF-456')
            ->setAmount(5000)
            ->setCurrencyCode(978)
            ->setCaptureFlag(1)
            ->setTokenData('tok-data')
            ->setReturnUrl('https://example.com/return');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-3',
            '0300',
            'merchant-001',
            'PAY-001',
            'ORD-123',
            'REF-456',
            '5000',
            '978',
            '1',
            'tok-data',
            'https://example.com/return',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testOptionalComplexFieldSettersAndGetters(): void
    {
        $request = new CardOnFilePaymentRequest();
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

    public function testComplexFieldsContributeToDigest(): void
    {
        $subMerchant = (new SubMerchantData())
            ->setMerchantId('SUB1')
            ->setMerchantType('5411')
            ->setMerchantName('Shop')
            ->setMerchantStreet('Street')
            ->setMerchantCity('City')
            ->setMerchantPostalCode('10000')
            ->setMerchantCountry('CZ')
            ->setMerchantWeb('www.s.cz')
            ->setMerchantServiceNumber('123');

        $altTerminal = (new AltTerminalData())->setTerminalId('T1');

        $request = new CardOnFilePaymentRequest();
        $request->setMessageId('msg-3')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-001')
            ->setAmount(5000)
            ->setCurrencyCode(978)
            ->setCaptureFlag(1)
            ->setSubMerchantData($subMerchant)
            ->setTokenData('tok-data')
            ->setAltTerminalData($altTerminal)
            ->setReturnUrl('https://example.com/return');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-3', '0300', 'merchant-001', 'PAY-001',
            '5000', '978', '1',
            $subMerchant->getDigest(),
            'tok-data',
            $altTerminal->getDigest(),
            'https://example.com/return',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testEmptyComplexFieldsDoNotAffectDigest(): void
    {
        $request = new CardOnFilePaymentRequest();
        $request->setMessageId('msg-3')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-001')
            ->setAmount(5000)
            ->setCurrencyCode(978)
            ->setCaptureFlag(1)
            ->setTokenData('tok-data')
            ->setReturnUrl('https://example.com/return')
            ->setSubMerchantData(new SubMerchantData())
            ->setCardHolderData(new CardHolderData())
            ->setPaymentInfo(new PaymentInfo())
            ->setShoppingCartInfo(new ShoppingCartInfo())
            ->setAltTerminalData(new AltTerminalData());

        // Empty sub-objects have getDigest() === null, so they are filtered out
        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-3', '0300', 'merchant-001', 'PAY-001',
            '5000', '978', '1', 'tok-data', 'https://example.com/return',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestWithoutOptionalFields(): void
    {
        $request = new CardOnFilePaymentRequest();
        $request->setMessageId('msg-3')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-001')
            ->setAmount(5000)
            ->setCurrencyCode(978)
            ->setCaptureFlag(1)
            ->setTokenData('tok-data')
            ->setReturnUrl('https://example.com/return');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-3',
            '0300',
            'merchant-001',
            'PAY-001',
            '5000',
            '978',
            '1',
            'tok-data',
            'https://example.com/return',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }
}
