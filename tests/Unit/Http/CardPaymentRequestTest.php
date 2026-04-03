<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Http;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Http\Exception\HttpRequestException;
use Pronnect\GpWebPay\Http\Request\CardPaymentRequest;

/**
 * @covers \Pronnect\GpWebPay\Http\Request\CardPaymentRequest
 */
class CardPaymentRequestTest extends TestCase
{
    private function makeRequest(): CardPaymentRequest
    {
        return new CardPaymentRequest(
            orderNumber: 123456,
            amount:      19900,
            currency:    203,
            depositFlag: 1,
            url:         'https://myshop.cz/callback',
        );
    }

    public function testToArrayContainsMandatoryFields(): void
    {
        $arr = $this->makeRequest()->toArray();

        $this->assertSame('CREATE_ORDER', $arr['OPERATION']);
        $this->assertSame(123456, $arr['ORDERNUMBER']);
        $this->assertSame(19900, $arr['AMOUNT']);
        $this->assertSame(203, $arr['CURRENCY']);
        $this->assertSame(1, $arr['DEPOSITFLAG']);
        $this->assertSame('https://myshop.cz/callback', $arr['URL']);
    }

    public function testToArrayExcludesNullOptionalFields(): void
    {
        $arr = $this->makeRequest()->toArray();

        $this->assertArrayNotHasKey('MERORDERNUM', $arr);
        $this->assertArrayNotHasKey('DESCRIPTION', $arr);
        $this->assertArrayNotHasKey('MD', $arr);
        $this->assertArrayNotHasKey('EMAIL', $arr);
        $this->assertArrayNotHasKey('ADDINFO', $arr);
    }

    public function testToArrayDoesNotIncludeLang(): void
    {
        $request = $this->makeRequest();
        $request->setLang('CS');
        $arr = $request->toArray();

        $this->assertArrayNotHasKey('LANG', $arr, 'LANG must not be in toArray() — gateway adds it after signing');
    }

    public function testToArrayDoesNotIncludeDigest(): void
    {
        $arr = $this->makeRequest()->toArray();

        $this->assertArrayNotHasKey('DIGEST', $arr);
    }

    public function testAmountZeroIsIncludedInToArray(): void
    {
        $request = new CardPaymentRequest(
            orderNumber: 999,
            amount:      0,
            currency:    203,
            depositFlag: 0,
            url:         'https://myshop.cz/callback',
        );

        $arr = $request->toArray();

        $this->assertArrayHasKey('AMOUNT', $arr, 'AMOUNT=0 must be present in toArray()');
        $this->assertSame(0, $arr['AMOUNT']);
    }

    public function testSetMdWithAsciiDoesNotEncode(): void
    {
        $request = $this->makeRequest();
        $request->setMd('ref-123');
        $arr = $request->toArray();

        $this->assertSame('ref-123', $arr['MD']);
    }

    public function testSetMdWithNonAsciiAutoEncodes(): void
    {
        $request = $this->makeRequest();
        $request->setMd('Referencia: číslo 123');
        $arr = $request->toArray();

        // Must be base64-encoded
        $this->assertNotSame('Referencia: číslo 123', $arr['MD']);
        $this->assertSame(base64_encode('Referencia: číslo 123'), $arr['MD']);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9+\/]+=*$/', (string) $arr['MD']);
    }

    public function testSetMdThrowsWhenExceeds255BytesAfterEncoding(): void
    {
        $this->expectException(HttpRequestException::class);
        $this->expectExceptionMessage('255');

        // Create a string that, after base64 encoding, will exceed 255 bytes
        // base64 increases size by ~33%, so ~192 chars of non-ASCII will exceed 255 bytes
        $longNonAscii = str_repeat('č', 200); // 200 × 2-byte UTF-8 chars → 400 bytes → base64 ~ 536 bytes

        $request = $this->makeRequest();
        $request->setMd($longNonAscii);
    }

    public function testSetMerOrderNumIssuedWarningWhenOver16Digits(): void
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

    public function testSetMerOrderNumNoWarningWhenExactly16Digits(): void
    {
        $request = $this->makeRequest();

        $warningIssued = false;
        set_error_handler(function () use (&$warningIssued): bool {
            $warningIssued = true;
            return true;
        });

        try {
            $request->setMerOrderNum(1234567890123456); // exactly 16 digits
        } finally {
            restore_error_handler();
        }

        $this->assertFalse($warningIssued, 'No warning should be issued for 16-digit MERORDERNUM');
    }

    public function testGetAddInfoReturnsNullByDefault(): void
    {
        $this->assertNull($this->makeRequest()->getAddInfo());
    }

    public function testGetAddInfoReturnsSetValue(): void
    {
        $request = $this->makeRequest();
        $request->setAddInfo('<additionalInfoRequest version="5.0"/>');

        $this->assertSame('<additionalInfoRequest version="5.0"/>', $request->getAddInfo());
    }

    public function testGetLangReturnsNull(): void
    {
        $this->assertNull($this->makeRequest()->getLang());
    }

    public function testGetLangReturnsUppercase(): void
    {
        $request = $this->makeRequest();
        $request->setLang('cs');

        $this->assertSame('CS', $request->getLang());
    }

    public function testToArrayIncludesOptionalFieldsWhenSet(): void
    {
        $request = $this->makeRequest();
        $request->setDescription('Order 123456');
        $request->setEmail('zakaznik@example.cz');
        $request->setMd('ref-abc');
        $request->setUserParam1('T');
        $request->setPanPattern('405607*******0016');

        $arr = $request->toArray();

        $this->assertSame('Order 123456', $arr['DESCRIPTION']);
        $this->assertSame('zakaznik@example.cz', $arr['EMAIL']);
        $this->assertSame('ref-abc', $arr['MD']);
        $this->assertSame('T', $arr['USERPARAM1']);
        $this->assertSame('405607*******0016', $arr['PANPATTERN']);
    }

    public function testToArrayIncludesRemainingOptionalFields(): void
    {
        $request = $this->makeRequest();
        $request->setVrCode('ENCRYPTED_VRCODE');
        $request->setFastPayId(42);
        $request->setPayMethod('CRD');
        $request->setPayMethods('CRD,GPAY');
        $request->setReferenceNumber('REF-001');
        $request->setToken('tok_abc');
        $request->setFastToken('ftok_xyz');

        $arr = $request->toArray();

        $this->assertSame('ENCRYPTED_VRCODE', $arr['VRCODE']);
        $this->assertSame(42, $arr['FASTPAYID']);
        $this->assertSame('CRD', $arr['PAYMETHOD']);
        $this->assertSame('CRD,GPAY', $arr['PAYMETHODS']);
        $this->assertSame('REF-001', $arr['REFERENCENUMBER']);
        $this->assertSame('tok_abc', $arr['TOKEN']);
        $this->assertSame('ftok_xyz', $arr['FASTTOKEN']);
    }
}
