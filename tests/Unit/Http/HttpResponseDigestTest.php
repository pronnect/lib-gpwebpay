<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Http;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Http\Digest\HttpResponseDigest;

/**
 * @covers \Pronnect\GpWebPay\Http\Digest\HttpResponseDigest
 */
class HttpResponseDigestTest extends TestCase
{
    private HttpResponseDigest $digest;

    protected function setUp(): void
    {
        $this->digest = new HttpResponseDigest();
    }

    private function callbackParams(): array
    {
        return [
            'OPERATION'   => 'CREATE_ORDER',
            'ORDERNUMBER' => '123456',
            'PRCODE'      => '0',
            'SRCODE'      => '0',
            'RESULTTEXT'  => 'OK',
        ];
    }

    public function testBuildForDigestIncludesResponseFields(): void
    {
        $params = $this->callbackParams();
        $result = $this->digest->buildForDigest($params);

        $this->assertSame('CREATE_ORDER|123456|0|0|OK', $result);
    }

    public function testBuildForDigestExcludesNullFields(): void
    {
        $params = [
            'OPERATION'   => 'CREATE_ORDER',
            'ORDERNUMBER' => '123456',
            'MERORDERNUM' => null,    // null — excluded
            'MD'          => null,    // null — excluded
            'PRCODE'      => '0',
            'SRCODE'      => '0',
            'RESULTTEXT'  => 'OK',
        ];

        $result = $this->digest->buildForDigest($params);

        $this->assertSame('CREATE_ORDER|123456|0|0|OK', $result);
    }

    public function testBuildForDigestExcludesEmptyStringFields(): void
    {
        $params = [
            'OPERATION'   => 'CREATE_ORDER',
            'ORDERNUMBER' => '123456',
            'MERORDERNUM' => '',   // empty — excluded
            'PRCODE'      => '0',
            'SRCODE'      => '0',
            'RESULTTEXT'  => 'OK',
        ];

        $result = $this->digest->buildForDigest($params);

        $this->assertSame('CREATE_ORDER|123456|0|0|OK', $result);
    }

    public function testBuildForDigest1AppendsMerchantNumber(): void
    {
        $params = array_merge($this->callbackParams(), [
            'MERCHANTNUMBER' => '0123456789',
        ]);

        $digest  = $this->digest->buildForDigest($params);
        $digest1 = $this->digest->buildForDigest1($params);

        // DIGEST1 = DIGEST fields + MERCHANTNUMBER at end
        $this->assertSame('CREATE_ORDER|123456|0|0|OK', $digest);
        $this->assertSame('CREATE_ORDER|123456|0|0|OK|0123456789', $digest1);
    }

    public function testDigestAndDigest1DifferByMerchantNumber(): void
    {
        $params = array_merge($this->callbackParams(), [
            'MERCHANTNUMBER' => '9876543210',
        ]);

        $digest  = $this->digest->buildForDigest($params);
        $digest1 = $this->digest->buildForDigest1($params);

        $this->assertNotSame($digest, $digest1);
        $this->assertStringEndsWith('|9876543210', $digest1);
        $this->assertStringNotContainsString('9876543210', $digest);
    }

    public function testBuildForDigest1ExcludesMerchantNumberFromMainFields(): void
    {
        // Even if MERCHANTNUMBER happens to appear in params, it must not
        // be included in the DIGEST (only in DIGEST1)
        $params = array_merge($this->callbackParams(), [
            'MERCHANTNUMBER' => '0123456789',
        ]);

        $digest = $this->digest->buildForDigest($params);

        $this->assertStringNotContainsString('0123456789', $digest);
    }

    public function testAllResponseFieldsInCorrectOrder(): void
    {
        $params = [
            'OPERATION'        => 'CREATE_ORDER',
            'ORDERNUMBER'      => '111',
            'MERORDERNUM'      => '222',
            'MD'               => 'myref',
            'PRCODE'           => '0',
            'SRCODE'           => '0',
            'RESULTTEXT'       => 'OK',
            'ADDINFO'          => '<xml/>',
            'TOKEN'            => 'tok123',
            'EXPIRY'           => '2612',
            'ACSRES'           => 'Y',
            'ACCODE'           => '01234567890123456789',
            'PANPATTERN'       => '405607*******0016',
            'DAYTOCAPTURE'     => '15032026',
            'TOKENREGSTATUS'   => 'SUCCESS',
            'ACRC'             => '00',
            'RRN'              => '123456789012',
            'PAR'              => 'PAR123',
            'TRACEID'          => 'TRACE456',
            'MERCHANTNUMBER'   => '0123456789',
        ];

        $digest  = $this->digest->buildForDigest($params);
        $digest1 = $this->digest->buildForDigest1($params);

        $expectedDigest  = 'CREATE_ORDER|111|222|myref|0|0|OK|<xml/>|tok123|2612|Y|01234567890123456789|405607*******0016|15032026|SUCCESS|00|123456789012|PAR123|TRACE456';
        $expectedDigest1 = $expectedDigest . '|0123456789';

        $this->assertSame($expectedDigest, $digest);
        $this->assertSame($expectedDigest1, $digest1);
    }

    public function testDigestFieldOrderHas19Fields(): void
    {
        $order = HttpResponseDigest::getDigestFieldOrder();
        $this->assertCount(19, $order, 'DIGEST field order must have exactly 19 fields');
        $this->assertNotContains('MERCHANTNUMBER', $order);
    }

    public function testDigest1FieldOrderHas20Fields(): void
    {
        $order = HttpResponseDigest::getDigest1FieldOrder();
        $this->assertCount(20, $order, 'DIGEST1 field order must have exactly 20 fields (19 + MERCHANTNUMBER)');
        $this->assertSame('MERCHANTNUMBER', end($order), 'MERCHANTNUMBER must be the last field in DIGEST1');
    }

    public function testDigest1FieldOrderStartsWithSameFieldsAsDigest(): void
    {
        $digestOrder  = HttpResponseDigest::getDigestFieldOrder();
        $digest1Order = HttpResponseDigest::getDigest1FieldOrder();

        // First 19 fields of DIGEST1 must match DIGEST exactly
        $this->assertSame($digestOrder, array_slice($digest1Order, 0, 19));
    }
}
