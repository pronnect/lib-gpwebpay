<?php

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\PaymentLinkRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * Class PaymentLinkRequestTest
 * @covers \Pronnect\GpWebPay\Request\PaymentLinkRequest
 * @uses \Pronnect\GpWebPay\Request\CardHolderData
 */
class PaymentLinkRequestTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetAndGetAmount(): void
    {
        $request = new PaymentLinkRequest();
        $request->setAmount(1000);
        $this->assertSame(1000, $request->getAmount());
    }

    /**
     * @return void
     */
    public function testSetAndGetCurrencyCode(): void
    {
        $request = new PaymentLinkRequest();
        $request->setCurrencyCode(978);
        $this->assertSame(978, $request->getCurrencyCode());
    }

    /**
     * @return void
     */
    public function testSetAndGetCaptureFlag(): void
    {
        $request = new PaymentLinkRequest();
        $request->setCaptureFlag(true);
        $this->assertTrue($request->getCaptureFlag());
    }

    /**
     * @return void
     */
    public function testSetAndGetOrderNumber(): void
    {
        $request = new PaymentLinkRequest();
        $request->setOrderNumber('123456');
        $this->assertSame('123456', $request->getOrderNumber());
    }

    /**
     * @return void
     */
    public function testSetAndGetReferenceNumber(): void
    {
        $request = new PaymentLinkRequest();
        $request->setReferenceNumber('654321');
        $this->assertSame('654321', $request->getReferenceNumber());
    }

    /**
     * @return void
     */
    public function testSetAndGetUrl(): void
    {
        $request = new PaymentLinkRequest();
        $request->setUrl('https://example.com');
        $this->assertSame('https://example.com', $request->getUrl());
    }

    /**
     * @return void
     */
    public function testSetAndGetDescription(): void
    {
        $request = new PaymentLinkRequest();
        $request->setDescription('Test description');
        $this->assertSame('Test description', $request->getDescription());
    }

    /**
     * @return void
     */
    public function testSetAndGetMerchantData(): void
    {
        $request = new PaymentLinkRequest();
        $request->setMerchantData('Test merchant data');
        $this->assertSame('Test merchant data', $request->getMerchantData());
    }

    /**
     * @return void
     */
    public function testSetAndGetEmail(): void
    {
        $request = new PaymentLinkRequest();
        $request->setEmail('test@example.com');
        $this->assertSame('test@example.com', $request->getEmail());
    }

    /**
     * @return void
     */
    public function testSetAndGetPaymentExpiry(): void
    {
        $request = new PaymentLinkRequest();
        $request->setPaymentExpiry('2023-12-31');
        $this->assertSame('2023-12-31', $request->getPaymentExpiry());
    }

    /**
     * @return void
     */
    public function testSetAndGetRegisterRecurring(): void
    {
        $request = new PaymentLinkRequest();
        $request->setRegisterRecurring(true);
        $this->assertTrue($request->getRegisterRecurring());
    }

    /**
     * @return void
     */
    public function testSetAndGetRegisterToken(): void
    {
        $request = new PaymentLinkRequest();
        $request->setRegisterToken(true);
        $this->assertTrue($request->getRegisterToken());
    }

    public function testSetAndGetFastPayId(): void
    {
        $request = new PaymentLinkRequest();
        $request->setFastPayId('FAST123');
        $this->assertSame('FAST123', $request->getFastPayId());
    }

    public function testSetAndGetDefaultPayMethod(): void
    {
        $request = new PaymentLinkRequest();
        $request->setDefaultPayMethod('CRD');
        $this->assertSame('CRD', $request->getDefaultPayMethod());
    }

    public function testSetAndGetDisabledPayMethods(): void
    {
        $request = new PaymentLinkRequest();
        $request->setDisabledPayMethods('CSH');
        $this->assertSame('CSH', $request->getDisabledPayMethods());
    }

    public function testSetAndGetPayMethods(): void
    {
        $request = new PaymentLinkRequest();
        $request->setPayMethods('CRD,CSH');
        $this->assertSame('CRD,CSH', $request->getPayMethods());
    }

    public function testSetAndGetMerchantEmail(): void
    {
        $request = new PaymentLinkRequest();
        $request->setMerchantEmail('merchant@example.com');
        $this->assertSame('merchant@example.com', $request->getMerchantEmail());
    }

    public function testSetAndGetLanguage(): void
    {
        $request = new PaymentLinkRequest();
        $request->setLanguage('cs');
        $this->assertSame('cs', $request->getLanguage());
    }

    public function testSetAndGetCardHolderData(): void
    {
        $request = new PaymentLinkRequest();
        $chd = new \Pronnect\GpWebPay\Request\CardHolderData();
        $request->setCardHolderData($chd);
        $this->assertSame($chd, $request->getCardHolderData());
    }

    /**
     * @return void
     */
    public function testGetDigestIncludesRegisterTokenWhenSet(): void
    {
        $request = new PaymentLinkRequest();
        $request->setMessageId('12345')
            ->setProvider('provider')
            ->setMerchantNumber('67890')
            ->setPaymentNumber('41234122')
            ->setAmount(1000)
            ->setCurrencyCode(978)
            ->setCaptureFlag(true)
            ->setRegisterToken(true);

        $digest = $request->getDigest();
        // registerToken=1 should be at the end, after language null (filtered) and registerRecurring null (filtered)
        $this->assertStringEndsWith('|1', $digest);
    }

    /**
     * @return void
     */
    public function testGetDigestWithValidData(): void
    {
        $request = new PaymentLinkRequest();
        $request->setMessageId('12345')
            ->setProvider('provider')
            ->setMerchantNumber('67890')
            ->setPaymentNumber('41234122')
            ->setAmount(1000)
            ->setCurrencyCode(978)
            ->setCaptureFlag(true)
            ->setOrderNumber('54321')
            ->setReferenceNumber('78966623514')
            ->setUrl('https://example.com')
            ->setDescription('Test description')
            ->setMerchantData('Test merchant data')
            ->setFastPayId('FAST1')
            ->setDefaultPayMethod('CRD')
            ->setDisabledPayMethods('CSH')
            ->setPayMethods('CRD,CSH')
            ->setEmail('test@example.com')
            ->setMerchantEmail('merchant@example.com')
            ->setPaymentExpiry('2023-12-31')
            ->setLanguage('cs')
            ->setRegisterRecurring(false)
            ->setRegisterToken(true);

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            '12345', 'provider', '67890', '41234122',
            '1000', '978', '1',
            '54321', '78966623514', 'https://example.com',
            'Test description', 'Test merchant data',
            'FAST1', 'CRD', 'CSH', 'CRD,CSH',
            'test@example.com', 'merchant@example.com',
            '2023-12-31', 'cs',
            '0', '1',
        ]);
        $this->assertSame($expectedDigest, $request->getDigest());
    }

    public function testGetDigestIncludesCardHolderData(): void
    {
        $chd = new \Pronnect\GpWebPay\Request\CardHolderData();
        $chd->setAddressMatch('Y');

        $request = new PaymentLinkRequest();
        $request->setMessageId('1')
            ->setProvider('p')
            ->setMerchantNumber('m')
            ->setPaymentNumber('pn')
            ->setAmount(100)
            ->setCurrencyCode(978)
            ->setCaptureFlag(true)
            ->setPaymentExpiry('2025-01-01')
            ->setCardHolderData($chd);

        $this->assertStringEndsWith('|Y', $request->getDigest());
    }
}
