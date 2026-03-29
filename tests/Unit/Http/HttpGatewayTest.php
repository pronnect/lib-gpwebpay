<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Http;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Http\Base64DigestSigner;
use Pronnect\GpWebPay\Http\Exception\HttpRequestException;
use Pronnect\GpWebPay\Http\Exception\InvalidCallbackException;
use Pronnect\GpWebPay\Http\Exception\InvalidDigestException;
use Pronnect\GpWebPay\Http\HttpGateway;
use Pronnect\GpWebPay\Http\Request\CardPaymentRequest;
use Pronnect\GpWebPayApi\DigestSignerInterface;
use Pronnect\GpWebPayApi\Http\HttpConfigInterface;
use Pronnect\GpWebPayApi\Http\Response\HttpResponseInterface;

/**
 * @covers \Pronnect\GpWebPay\Http\HttpGateway
 * @uses   \Pronnect\GpWebPay\Http\Base64DigestSigner
 * @uses   \Pronnect\GpWebPay\Http\Digest\HttpRequestDigest
 * @uses   \Pronnect\GpWebPay\Http\Digest\HttpResponseDigest
 * @uses   \Pronnect\GpWebPay\Http\Request\CardPaymentRequest
 * @uses   \Pronnect\GpWebPay\Http\Response\HttpResponse
 */
class HttpGatewayTest extends TestCase
{
    private HttpConfigInterface $config;
    private Base64DigestSigner $signer;

    protected function setUp(): void
    {
        $this->config = $this->createMock(HttpConfigInterface::class);
        $this->config->method('getMerchantNumber')->willReturn('0123456789');
        $this->config->method('getHttpUri')->willReturn('https://test.3dsecure.gpwebpay.com/pgw/order.do');
        $this->config->method('getDefaultLang')->willReturn(null);

        $mockInner = $this->createMock(DigestSignerInterface::class);
        $mockInner->method('sign')->willReturn('raw-binary-sig');
        $mockInner->method('verify')->willReturn(true);

        $this->signer = new Base64DigestSigner($mockInner);
    }

    private function makeGateway(): HttpGateway
    {
        return new HttpGateway($this->config, $this->signer);
    }

    private function makeRequest(bool $withAddInfo = false): CardPaymentRequest
    {
        $request = new CardPaymentRequest(
            orderNumber: 123456,
            amount:      19900,
            currency:    203,
            depositFlag: 1,
            url:         'https://myshop.cz/callback',
        );

        if ($withAddInfo) {
            $request->setAddInfo('<additionalInfoRequest version="5.0"></additionalInfoRequest>');
        }

        return $request;
    }

    // ── getRedirectUrl ────────────────────────────────────────────────────────

    public function testGetRedirectUrlContainsEndpointAndParams(): void
    {
        $url = $this->makeGateway()->getRedirectUrl($this->makeRequest());

        $this->assertStringStartsWith('https://test.3dsecure.gpwebpay.com/pgw/order.do?', $url);
        $this->assertStringContainsString('MERCHANTNUMBER=0123456789', $url);
        $this->assertStringContainsString('ORDERNUMBER=123456', $url);
        $this->assertStringContainsString('DIGEST=', $url);
    }

    public function testGetRedirectUrlContainsBase64Digest(): void
    {
        $url = $this->makeGateway()->getRedirectUrl($this->makeRequest());

        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $digest = $params['DIGEST'] ?? '';

        $this->assertNotEmpty($digest);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9+\/]+=*$/', $digest);
    }

    public function testGetRedirectUrlDoesNotContainLangInDigestButInUrl(): void
    {
        $this->config->method('getDefaultLang') // already stubbed but override
        ;
        $config = $this->createMock(HttpConfigInterface::class);
        $config->method('getMerchantNumber')->willReturn('0123456789');
        $config->method('getHttpUri')->willReturn('https://test.example.com/pgw/order.do');
        $config->method('getDefaultLang')->willReturn('CS');

        $gateway = new HttpGateway($config, $this->signer);
        $url     = $gateway->getRedirectUrl($this->makeRequest());

        // LANG must be present in URL
        $this->assertStringContainsString('LANG=CS', $url);

        // Verify that sign() was called WITHOUT 'CS' in the digest string
        $mockInner = $this->createMock(DigestSignerInterface::class);
        $mockInner->expects($this->once())
            ->method('sign')
            ->with($this->logicalNot($this->stringContains('CS')))
            ->willReturn('sig');

        $signer  = new Base64DigestSigner($mockInner);
        $gateway2 = new HttpGateway($config, $signer);
        $gateway2->getRedirectUrl($this->makeRequest());
    }

    public function testGetRedirectUrlThrowsWhenAddInfoIsSet(): void
    {
        $this->expectException(HttpRequestException::class);
        $this->expectExceptionMessage('POST');

        $this->makeGateway()->getRedirectUrl($this->makeRequest(withAddInfo: true));
    }

    // ── getFormParams ─────────────────────────────────────────────────────────

    public function testGetFormParamsReturnsArray(): void
    {
        $params = $this->makeGateway()->getFormParams($this->makeRequest());

        $this->assertIsArray($params);
        $this->assertArrayHasKey('MERCHANTNUMBER', $params);
        $this->assertArrayHasKey('DIGEST', $params);
        $this->assertArrayNotHasKey('LANG', $params); // no default lang configured
    }

    public function testGetFormParamsAllowsAddInfo(): void
    {
        // Must NOT throw when ADDINFO is present (POST is allowed)
        $params = $this->makeGateway()->getFormParams($this->makeRequest(withAddInfo: true));

        $this->assertArrayHasKey('ADDINFO', $params);
    }

    public function testGetFormParamsMerchantNumberIsFromConfig(): void
    {
        $params = $this->makeGateway()->getFormParams($this->makeRequest());

        $this->assertSame('0123456789', $params['MERCHANTNUMBER']);
    }

    // ── processCallback ───────────────────────────────────────────────────────

    public function testProcessCallbackThrowsWhenDigestIsMissing(): void
    {
        $this->expectException(InvalidCallbackException::class);

        $this->makeGateway()->processCallback([
            'OPERATION'   => 'CREATE_ORDER',
            'ORDERNUMBER' => '123456',
            'PRCODE'      => '0',
            'SRCODE'      => '0',
        ]);
    }

    public function testProcessCallbackThrowsWhenDigestIsInvalid(): void
    {
        $this->expectException(InvalidDigestException::class);

        $mockInner = $this->createMock(DigestSignerInterface::class);
        $mockInner->method('verify')->willReturn(false); // always fails

        $signer  = new Base64DigestSigner($mockInner);
        $gateway = new HttpGateway($this->config, $signer);

        $gateway->processCallback([
            'OPERATION'   => 'CREATE_ORDER',
            'ORDERNUMBER' => '123456',
            'PRCODE'      => '0',
            'SRCODE'      => '0',
            'DIGEST'      => base64_encode('bad-signature'),
        ]);
    }

    public function testProcessCallbackThrowsWhenDigest1IsInvalid(): void
    {
        $this->expectException(InvalidDigestException::class);

        // DIGEST verifies OK, DIGEST1 fails
        $mockInner = $this->createMock(DigestSignerInterface::class);
        $mockInner->method('verify')
            ->willReturnOnConsecutiveCalls(true, false); // DIGEST=ok, DIGEST1=fail

        $signer  = new Base64DigestSigner($mockInner);
        $gateway = new HttpGateway($this->config, $signer);

        $gateway->processCallback([
            'OPERATION'   => 'CREATE_ORDER',
            'ORDERNUMBER' => '123456',
            'PRCODE'      => '0',
            'SRCODE'      => '0',
            'DIGEST'      => base64_encode('valid-sig'),
            'DIGEST1'     => base64_encode('invalid-sig'),
        ]);
    }

    public function testProcessCallbackSucceedsWithValidDigest(): void
    {
        // signer mock always returns true
        $response = $this->makeGateway()->processCallback([
            'OPERATION'   => 'CREATE_ORDER',
            'ORDERNUMBER' => '123456',
            'PRCODE'      => '0',
            'SRCODE'      => '0',
            'RESULTTEXT'  => 'OK',
            'DIGEST'      => base64_encode('valid-sig'),
        ]);

        $this->assertInstanceOf(HttpResponseInterface::class, $response);
        $this->assertTrue($response->isSuccess());
        $this->assertSame(123456, $response->getOrderNumber());
    }

    public function testProcessCallbackSkipsDigest1WhenAbsent(): void
    {
        // verify() should be called only once (for DIGEST), not twice
        $mockInner = $this->createMock(DigestSignerInterface::class);
        $mockInner->expects($this->once())
            ->method('verify')
            ->willReturn(true);

        $signer  = new Base64DigestSigner($mockInner);
        $gateway = new HttpGateway($this->config, $signer);

        $gateway->processCallback([
            'OPERATION'   => 'CREATE_ORDER',
            'ORDERNUMBER' => '123456',
            'PRCODE'      => '0',
            'SRCODE'      => '0',
            'DIGEST'      => base64_encode('valid'),
            // No DIGEST1
        ]);
    }

    // ── getHttpUri ────────────────────────────────────────────────────────────

    public function testGetHttpUriReturnsConfigValue(): void
    {
        $uri = $this->makeGateway()->getHttpUri();
        $this->assertSame('https://test.3dsecure.gpwebpay.com/pgw/order.do', $uri);
    }

    // ── create factory ────────────────────────────────────────────────────────

    public function testCreateFactoryReturnsHttpGatewayInstance(): void
    {
        $rawSigner = $this->createMock(\Pronnect\GpWebPay\DigestSigner::class);
        $gateway   = HttpGateway::create($this->config, $rawSigner);

        $this->assertInstanceOf(HttpGateway::class, $gateway);
    }

    public function testCreateFactoryGatewayBuildsRedirectUrl(): void
    {
        // Create a real DigestSigner with test certs so create() works end-to-end
        $mockInner = $this->createMock(\Pronnect\GpWebPayApi\DigestSignerInterface::class);
        $mockInner->method('sign')->willReturn('sig');
        $mockInner->method('verify')->willReturn(true);

        // Use the constructor directly to get a DigestSigner-compatible mock
        // create() wraps it in Base64DigestSigner internally
        $rawSigner = $this->getMockBuilder(\Pronnect\GpWebPay\DigestSigner::class)
            ->disableOriginalConstructor()
            ->getMock();
        $rawSigner->method('sign')->willReturn('raw-sig');

        $gateway = HttpGateway::create($this->config, $rawSigner);
        $url     = $gateway->getRedirectUrl($this->makeRequest());

        $this->assertStringStartsWith('https://test.3dsecure.gpwebpay.com/pgw/order.do?', $url);
    }
}
