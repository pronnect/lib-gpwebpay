<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Http;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Http\Request\GooglePayRequest;

/**
 * @covers \Pronnect\GpWebPay\Http\Request\GooglePayRequest
 * @uses   \Pronnect\GpWebPay\Http\Request\CardPaymentRequest
 */
class GooglePayRequestTest extends TestCase
{
    private function makeRequest(): GooglePayRequest
    {
        return new GooglePayRequest(
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

        $this->assertSame('GPAY', $arr['PAYMETHOD']);
    }

    public function testToArrayContainsPayMethods(): void
    {
        $arr = $this->makeRequest()->toArray();

        $this->assertSame('GPAY', $arr['PAYMETHODS']);
    }

    public function testSetPayMethodThrowsLogicException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('GooglePayRequest');

        $this->makeRequest()->setPayMethod('CRD');
    }

    public function testSetPayMethodsThrowsLogicException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('GooglePayRequest');

        $this->makeRequest()->setPayMethods('CRD,APAY');
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
