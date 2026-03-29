<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Http;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Http\Exception\HttpRequestException;
use Pronnect\GpWebPay\Http\Request\CardVerificationRequest;

/**
 * @covers \Pronnect\GpWebPay\Http\Request\CardVerificationRequest
 */
class CardVerificationRequestTest extends TestCase
{
    private function makeRequest(): CardVerificationRequest
    {
        return new CardVerificationRequest(
            orderNumber: 999001,
            url:         'https://myshop.cz/callback',
        );
    }

    public function testToArrayContainsMandatoryFields(): void
    {
        $arr = $this->makeRequest()->toArray();

        $this->assertSame('CARD_VERIFICATION', $arr['OPERATION']);
        $this->assertSame(999001, $arr['ORDERNUMBER']);
        $this->assertSame('https://myshop.cz/callback', $arr['URL']);
    }

    public function testToArrayExcludesNullOptionalFields(): void
    {
        $arr = $this->makeRequest()->toArray();

        $this->assertArrayNotHasKey('AMOUNT', $arr);
        $this->assertArrayNotHasKey('CURRENCY', $arr);
        $this->assertArrayNotHasKey('DEPOSITFLAG', $arr);
        $this->assertArrayNotHasKey('MERORDERNUM', $arr);
        $this->assertArrayNotHasKey('DESCRIPTION', $arr);
        $this->assertArrayNotHasKey('MD', $arr);
        $this->assertArrayNotHasKey('USERPARAM1', $arr);
        $this->assertArrayNotHasKey('VRCODE', $arr);
        $this->assertArrayNotHasKey('PAYMETHOD', $arr);
        $this->assertArrayNotHasKey('PAYMETHODS', $arr);
        $this->assertArrayNotHasKey('EMAIL', $arr);
        $this->assertArrayNotHasKey('REFERENCENUMBER', $arr);
        $this->assertArrayNotHasKey('ADDINFO', $arr);
        $this->assertArrayNotHasKey('PANPATTERN', $arr);
        $this->assertArrayNotHasKey('TOKEN', $arr);
    }

    public function testOptionalAmountCurrencyDepositFlagIncludedWhenSet(): void
    {
        $request = $this->makeRequest();
        $request->setAmount(100);
        $request->setCurrency(203);
        $request->setDepositFlag(0);

        $arr = $request->toArray();

        $this->assertSame(100, $arr['AMOUNT']);
        $this->assertSame(203, $arr['CURRENCY']);
        $this->assertSame(0, $arr['DEPOSITFLAG']);
    }

    public function testSetMerOrderNumWarningWhenOver16Digits(): void
    {
        $request = $this->makeRequest();

        set_error_handler(function (int $errno, string $errstr): bool {
            $this->assertSame(E_USER_WARNING, $errno);
            $this->assertStringContainsString('16', $errstr);
            return true;
        });

        try {
            $request->setMerOrderNum(12345678901234567); // 17 digits
        } finally {
            restore_error_handler();
        }
    }

    public function testSetMerOrderNumNoWarningFor16Digits(): void
    {
        $warningIssued = false;
        set_error_handler(function () use (&$warningIssued): bool {
            $warningIssued = true;
            return true;
        });

        try {
            $this->makeRequest()->setMerOrderNum(1234567890123456);
        } finally {
            restore_error_handler();
        }

        $this->assertFalse($warningIssued);
    }

    public function testSetMdAsciiDoesNotEncode(): void
    {
        $request = $this->makeRequest();
        $request->setMd('ref-456');

        $this->assertSame('ref-456', $request->toArray()['MD']);
    }

    public function testSetMdNonAsciiAutoEncodes(): void
    {
        $request = $this->makeRequest();
        $request->setMd('Referencia: číslo');

        $arr = $request->toArray();
        $this->assertSame(base64_encode('Referencia: číslo'), $arr['MD']);
    }

    public function testSetMdThrowsWhenExceeds255BytesAfterEncoding(): void
    {
        $this->expectException(HttpRequestException::class);
        $this->expectExceptionMessage('255');

        $this->makeRequest()->setMd(str_repeat('č', 200));
    }

    public function testGetAddInfoReturnsNullByDefault(): void
    {
        $this->assertNull($this->makeRequest()->getAddInfo());
    }

    public function testSetAddInfoStoresValue(): void
    {
        $request = $this->makeRequest();
        $request->setAddInfo('<additionalInfoRequest version="5.0"/>');

        $this->assertSame('<additionalInfoRequest version="5.0"/>', $request->getAddInfo());
    }

    public function testGetLangReturnsNullByDefault(): void
    {
        $this->assertNull($this->makeRequest()->getLang());
    }

    public function testSetLangConvertsToUppercase(): void
    {
        $request = $this->makeRequest();
        $request->setLang('cs');

        $this->assertSame('CS', $request->getLang());
    }

    public function testToArrayDoesNotIncludeLang(): void
    {
        $request = $this->makeRequest();
        $request->setLang('SK');

        $this->assertArrayNotHasKey('LANG', $request->toArray(), 'LANG must not be in toArray() — gateway adds it after signing');
    }

    public function testAllOptionalFieldsIncludedWhenSet(): void
    {
        $request = $this->makeRequest();
        $request->setDescription('COF registration');
        $request->setUserParam1('T');
        $request->setVrCode('AABBCC');
        $request->setPayMethod('CRD');
        $request->setPayMethods('CRD,GPAY');
        $request->setEmail('test@example.cz');
        $request->setReferenceNumber('REF001');
        $request->setPanPattern('405607*******0016');
        $request->setToken('tok_abc123');

        $arr = $request->toArray();

        $this->assertSame('COF registration', $arr['DESCRIPTION']);
        $this->assertSame('T', $arr['USERPARAM1']);
        $this->assertSame('AABBCC', $arr['VRCODE']);
        $this->assertSame('CRD', $arr['PAYMETHOD']);
        $this->assertSame('CRD,GPAY', $arr['PAYMETHODS']);
        $this->assertSame('test@example.cz', $arr['EMAIL']);
        $this->assertSame('REF001', $arr['REFERENCENUMBER']);
        $this->assertSame('405607*******0016', $arr['PANPATTERN']);
        $this->assertSame('tok_abc123', $arr['TOKEN']);
    }
}