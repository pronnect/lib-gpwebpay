<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Http;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Http\Request\ApplePayRequest;

/**
 * @covers \Pronnect\GpWebPay\Http\Request\ApplePayRequest
 * @uses   \Pronnect\GpWebPay\Http\Request\CardPaymentRequest
 */
class ApplePayRequestTest extends TestCase
{
    private function makeRequest(): ApplePayRequest
    {
        return new ApplePayRequest(
            orderNumber: 123456,
            amount:      19900,
            currency:    203,
            depositFlag: 1,
            url:         'https://myshop.cz/callback',
        );
    }

    public function testToArrayContainsPayMethod(): void
    {
        $arr = $this->makeRequest()->toArray();

        $this->assertSame('APAY', $arr['PAYMETHOD']);
    }

    public function testToArrayContainsPayMethods(): void
    {
        $arr = $this->makeRequest()->toArray();

        $this->assertSame('APAY', $arr['PAYMETHODS']);
    }

    public function testSetPayMethodThrowsLogicException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('ApplePayRequest');

        $this->makeRequest()->setPayMethod('CRD');
    }

    public function testSetPayMethodsThrowsLogicException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('ApplePayRequest');

        $this->makeRequest()->setPayMethods('CRD,GPAY');
    }

    public function testMandatoryFieldsAreStillPresent(): void
    {
        $arr = $this->makeRequest()->toArray();

        $this->assertSame('CREATE_ORDER', $arr['OPERATION']);
        $this->assertSame(123456, $arr['ORDERNUMBER']);
        $this->assertSame(19900, $arr['AMOUNT']);
        $this->assertSame(203, $arr['CURRENCY']);
        $this->assertSame(1, $arr['DEPOSITFLAG']);
        $this->assertSame('https://myshop.cz/callback', $arr['URL']);
    }
}
