<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\AdditionalInfoResponse;
use Pronnect\GpWebPay\Response\PaymentDetailResponse;
use Pronnect\GpWebPay\Response\SimpleValue;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\AdditionalInfoResponseInterface;
use Pronnect\GpWebPayApi\Response\SimpleValueInterface;

/**
 * @covers \Pronnect\GpWebPay\Response\PaymentDetailResponse
 * @covers \Pronnect\GpWebPay\Response\Response::__set
 */
class PaymentDetailResponseTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetPaymentMethod(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->paymentMethod = 'Credit Card';

        $this->assertSame('Credit Card', $paymentDetailResponse->getPaymentMethod());
    }

    /**
     * @return void
     */
    public function testGetPanMasked(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->panMasked = '1234********5678';

        $this->assertSame('1234********5678', $paymentDetailResponse->getPanMasked());
    }

    /**
     * @return void
     */
    public function testGetBrandName(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->brandName = 'Visa';

        $this->assertSame('Visa', $paymentDetailResponse->getBrandName());
    }

    /**
     * @return void
     */
    public function testGetPaymentAmount(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->paymentAmount = '100.00';

        $this->assertSame('100.00', $paymentDetailResponse->getPaymentAmount());
    }

    /**
     * @return void
     */
    public function testGetApproveAmount(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->approveAmount = '100.00';

        $this->assertSame('100.00', $paymentDetailResponse->getApproveAmount());
    }

    /**
     * @return void
     */
    public function testGetCaptureAmount(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->captureAmount = '100.00';

        $this->assertSame('100.00', $paymentDetailResponse->getCaptureAmount());
    }

    /**
     * @return void
     */
    public function testGetRefundAmount(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->refundAmount = '50.00';

        $this->assertSame('50.00', $paymentDetailResponse->getRefundAmount());
    }

    /**
     * @return void
     */
    public function testGetApproveCode(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->approveCode = 'APPROVED';

        $this->assertSame('APPROVED', $paymentDetailResponse->getApproveCode());
    }

    /**
     * @return void
     */
    public function testGetPaymentTime(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->paymentTime = '2023-10-01T12:00:00Z';

        $this->assertSame('2023-10-01T12:00:00Z', $paymentDetailResponse->getPaymentTime());
    }

    /**
     * @return void
     */
    public function testGetApproveTime(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->approveTime = '2023-10-01T12:05:00Z';

        $this->assertSame('2023-10-01T12:05:00Z', $paymentDetailResponse->getApproveTime());
    }

    /**
     * @return void
     */
    public function testGetLastCaptureTime(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->lastCaptureTime = '2023-10-01T12:10:00Z';

        $this->assertSame('2023-10-01T12:10:00Z', $paymentDetailResponse->getLastCaptureTime());
    }

    /**
     * @return void
     */
    public function testGetAdditionalInfo(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $additionalInfoMock = $this->createMock(AdditionalInfoResponseInterface::class);
        $paymentDetailResponse->additionalInfo = $additionalInfoMock;

        $this->assertSame($additionalInfoMock, $paymentDetailResponse->getAdditionalInfo());
    }

    /**
     * @return void
     */
    public function testGetSimpleValueHolder(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $simpleValueMock = $this->createMock(SimpleValueInterface::class);
        $paymentDetailResponse->simpleValueHolder = [$simpleValueMock];

        $this->assertSame([$simpleValueMock], $paymentDetailResponse->getSimpleValueHolder());
    }

    /**
     * @return void
     */
    public function testGetPanToken(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->panToken = 'token123';

        $this->assertSame('token123', $paymentDetailResponse->getPanToken());
    }

    /**
     * @return void
     */
    public function testGetPanPattern(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->panPattern = '**** **** **** 1234';

        $this->assertSame('**** **** **** 1234', $paymentDetailResponse->getPanPattern());
    }

    /**
     * @return void
     */
    public function testGetPanExpiry(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->panExpiry = '12/25';

        $this->assertSame('12/25', $paymentDetailResponse->getPanExpiry());
    }

    /**
     * @return void
     */
    public function testGetAcsResult(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->acsResult = 'Y';

        $this->assertSame('Y', $paymentDetailResponse->getAcsResult());
    }

    /**
     * @return void
     */
    public function testGetDayToCapture(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->dayToCapture = '5';

        $this->assertSame('5', $paymentDetailResponse->getDayToCapture());
    }

    /**
     * @return void
     */
    public function testGetTraceId(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->traceId = 'trace123';

        $this->assertSame('trace123', $paymentDetailResponse->getTraceId());
    }

    /**
     * @return void
     */
    public function testGetAuthResponseCode(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->authResponseCode = '00';

        $this->assertSame('00', $paymentDetailResponse->getAuthResponseCode());
    }

    /**
     * @return void
     */
    public function testGetAuthRRN(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->authRRN = 'rrn123';

        $this->assertSame('rrn123', $paymentDetailResponse->getAuthRRN());
    }

    /**
     * @return void
     */
    public function testGetPaymentAccountReference(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->paymentAccountReference = 'ref123';

        $this->assertSame('ref123', $paymentDetailResponse->getPaymentAccountReference());
    }

    /**
     * @return void
     */
    public function testGetIasId(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->iasId = 'ias123';

        $this->assertSame('ias123', $paymentDetailResponse->getIasId());
    }

    /**
     * @return void
     */
    public function testGetPayPalId(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->payPalId = 'paypal123';

        $this->assertSame('paypal123', $paymentDetailResponse->getPayPalId());
    }

    /**
     * @covers \Pronnect\GpWebPay\Response\StateResponse::getState
     * @covers \Pronnect\GpWebPay\Response\StateResponse::getSubStatus
     * @covers \Pronnect\GpWebPay\Response\StatusResponse::getStatus
     *
     * @return void
     */
    public function testGetDigest(): void
    {
        $paymentDetailResponse = new PaymentDetailResponse();
        $paymentDetailResponse->messageId = "messageId";
        $paymentDetailResponse->state = 'state';
        $paymentDetailResponse->status = 'status';
        $paymentDetailResponse->subStatus = 'subStatus';
        $paymentDetailResponse->paymentMethod = 'Credit Card';
        $paymentDetailResponse->panMasked = '1234********5678';
        $paymentDetailResponse->brandName = 'Visa';
        $paymentDetailResponse->paymentAmount = '100.00';
        $paymentDetailResponse->approveAmount = '100.00';
        $paymentDetailResponse->captureAmount = '100.00';
        $paymentDetailResponse->refundAmount = '50.00';
        $paymentDetailResponse->approveCode = 'APPROVED';
        $paymentDetailResponse->paymentTime = '2023-10-01T12:00:00Z';
        $paymentDetailResponse->approveTime = '2023-10-01T12:05:00Z';
        $paymentDetailResponse->lastCaptureTime = '2023-10-01T12:10:00Z';
        $additionalInfoMock = $this->createMock(AdditionalInfoResponse::class);
        $additionalInfoMock->method('getDigest')->willReturn('additionalInfoDigest');
        $paymentDetailResponse->additionalInfo = $additionalInfoMock;
        $simpleValueMock = $this->createMock(SimpleValue::class);
        $simpleValueMock->method('getDigest')->willReturn('simpleValueDigest');
        $paymentDetailResponse->simpleValueHolder = [$simpleValueMock];
        $paymentDetailResponse->panToken = 'token123';
        $paymentDetailResponse->panPattern = '**** **** **** 1234';
        $paymentDetailResponse->panExpiry = '12/25';
        $paymentDetailResponse->acsResult = 'Y';
        $paymentDetailResponse->dayToCapture = '5';
        $paymentDetailResponse->traceId = 'trace123';
        $paymentDetailResponse->authResponseCode = '00';
        $paymentDetailResponse->authRRN = 'rrn123';
        $paymentDetailResponse->paymentAccountReference = 'ref123';
        $paymentDetailResponse->iasId = 'ias123';
        $paymentDetailResponse->payPalId = 'paypal123';

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            'messageId',
            'state',
            'status',
            'subStatus',
            'Credit Card',
            '1234********5678',
            'Visa',
            '100.00',
            '100.00',
            '100.00',
            '50.00',
            'APPROVED',
            '2023-10-01T12:00:00Z',
            '2023-10-01T12:05:00Z',
            '2023-10-01T12:10:00Z',
            'additionalInfoDigest',
            'simpleValueDigest',
            'token123',
            '**** **** **** 1234',
            '12/25',
            'Y',
            '5',
            'trace123',
            '00',
            'rrn123',
            'ref123',
            'ias123',
            'paypal123',
        ]);
        $this->assertSame($expectedDigest, $paymentDetailResponse->getDigest());
    }
}
