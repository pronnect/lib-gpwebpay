<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Http;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Http\Digest\HttpRequestDigest;

/**
 * @covers \Pronnect\GpWebPay\Http\Digest\HttpRequestDigest
 */
class HttpRequestDigestTest extends TestCase
{
    private HttpRequestDigest $digest;

    protected function setUp(): void
    {
        $this->digest = new HttpRequestDigest();
    }

    public function testBasicDigestWithMandatoryFields(): void
    {
        $params = [
            'MERCHANTNUMBER' => '0123456789',
            'OPERATION'      => 'CREATE_ORDER',
            'ORDERNUMBER'    => '123456',
            'AMOUNT'         => '19900',
            'CURRENCY'       => '203',
            'DEPOSITFLAG'    => '1',
            'URL'            => 'https://myshop.cz/callback',
        ];

        $result = $this->digest->build($params);

        $this->assertSame(
            '0123456789|CREATE_ORDER|123456|19900|203|1|https://myshop.cz/callback',
            $result,
        );
    }

    public function testNullFieldsAreExcluded(): void
    {
        $params = [
            'MERCHANTNUMBER' => '0123456789',
            'OPERATION'      => 'CREATE_ORDER',
            'ORDERNUMBER'    => '123456',
            'AMOUNT'         => '19900',
            'CURRENCY'       => null,  // null — excluded
            'DEPOSITFLAG'    => '1',
            'URL'            => 'https://myshop.cz/callback',
        ];

        $result = $this->digest->build($params);

        $this->assertSame(
            '0123456789|CREATE_ORDER|123456|19900|1|https://myshop.cz/callback',
            $result,
        );
    }

    public function testEmptyStringFieldsAreExcluded(): void
    {
        $params = [
            'MERCHANTNUMBER' => '0123456789',
            'OPERATION'      => 'CREATE_ORDER',
            'ORDERNUMBER'    => '123456',
            'AMOUNT'         => '19900',
            'CURRENCY'       => '',    // empty string — excluded
            'DEPOSITFLAG'    => '1',
            'URL'            => 'https://myshop.cz/callback',
        ];

        $result = $this->digest->build($params);

        $this->assertSame(
            '0123456789|CREATE_ORDER|123456|19900|1|https://myshop.cz/callback',
            $result,
        );
    }

    /**
     * AMOUNT = 0 is a valid value for CARD_VERIFICATION.
     * Must NOT be excluded (would break signature).
     */
    public function testAmountZeroIsIncludedInDigest(): void
    {
        $params = [
            'MERCHANTNUMBER' => '0123456789',
            'OPERATION'      => 'CARD_VERIFICATION',
            'ORDERNUMBER'    => '123456',
            'AMOUNT'         => 0,     // integer zero — must be included
            'URL'            => 'https://myshop.cz/callback',
        ];

        $result = $this->digest->build($params);

        $this->assertStringContainsString('|0|', $result, 'AMOUNT=0 must be present in digest');
        $this->assertSame(
            '0123456789|CARD_VERIFICATION|123456|0|https://myshop.cz/callback',
            $result,
        );
    }

    public function testAmountZeroAsStringIsIncluded(): void
    {
        $params = [
            'MERCHANTNUMBER' => '0123456789',
            'OPERATION'      => 'CARD_VERIFICATION',
            'ORDERNUMBER'    => '123456',
            'AMOUNT'         => '0',   // string "0" — must also be included
            'URL'            => 'https://myshop.cz/callback',
        ];

        $result = $this->digest->build($params);

        $this->assertStringContainsString('|0|', $result);
    }

    public function testLangIsNeverIncludedInDigest(): void
    {
        $params = [
            'MERCHANTNUMBER' => '0123456789',
            'OPERATION'      => 'CREATE_ORDER',
            'ORDERNUMBER'    => '123456',
            'AMOUNT'         => '19900',
            'CURRENCY'       => '203',
            'DEPOSITFLAG'    => '1',
            'URL'            => 'https://myshop.cz/callback',
            'LANG'           => 'CS',  // LANG must never be in digest
        ];

        $result = $this->digest->build($params);

        $this->assertStringNotContainsString('CS', $result, 'LANG must NOT appear in digest string');
    }

    public function testDigestIsNeverIncludedInDigestString(): void
    {
        $params = [
            'MERCHANTNUMBER' => '0123456789',
            'OPERATION'      => 'CREATE_ORDER',
            'ORDERNUMBER'    => '123456',
            'AMOUNT'         => '19900',
            'URL'            => 'https://myshop.cz/callback',
            'DIGEST'         => 'SomeBase64Signature==', // must never appear in digest
        ];

        $result = $this->digest->build($params);

        $this->assertStringNotContainsString('SomeBase64Signature', $result);
    }

    public function testFieldOrderIsPreserved(): void
    {
        // Give fields in wrong order; digest must always use spec order
        $params = [
            'URL'            => 'https://myshop.cz/callback',
            'OPERATION'      => 'CREATE_ORDER',
            'AMOUNT'         => '19900',
            'MERCHANTNUMBER' => '0123456789',
            'ORDERNUMBER'    => '123456',
        ];

        $result = $this->digest->build($params);

        // Expected: MERCHANTNUMBER | OPERATION | ORDERNUMBER | AMOUNT | URL
        $this->assertSame(
            '0123456789|CREATE_ORDER|123456|19900|https://myshop.cz/callback',
            $result,
        );
    }

    public function testOptionalFieldsIncludedWhenPresent(): void
    {
        $params = [
            'MERCHANTNUMBER' => '0123456789',
            'OPERATION'      => 'CREATE_ORDER',
            'ORDERNUMBER'    => '123456',
            'AMOUNT'         => '19900',
            'CURRENCY'       => '203',
            'DEPOSITFLAG'    => '1',
            'MERORDERNUM'    => '987654',
            'URL'            => 'https://myshop.cz/callback',
            'DESCRIPTION'    => 'Order 123456',
            'MD'             => 'ref-123',
            'EMAIL'          => 'zakaznik@example.cz',
        ];

        $result = $this->digest->build($params);

        $this->assertSame(
            '0123456789|CREATE_ORDER|123456|19900|203|1|987654|https://myshop.cz/callback|Order 123456|ref-123|zakaznik@example.cz',
            $result,
        );
    }

    public function testEmptyParamsProduceEmptyString(): void
    {
        $result = $this->digest->build([]);

        $this->assertSame('', $result);
    }

    public function testGetFieldOrderContainsRequiredFields(): void
    {
        $order = HttpRequestDigest::getFieldOrder();

        $this->assertContains('MERCHANTNUMBER', $order);
        $this->assertContains('OPERATION', $order);
        $this->assertContains('ORDERNUMBER', $order);
        $this->assertContains('AMOUNT', $order);
        $this->assertContains('ADDINFO', $order);
        $this->assertNotContains('LANG', $order, 'LANG must never be in field order');
        $this->assertNotContains('DIGEST', $order, 'DIGEST must never be in field order');
    }
}
