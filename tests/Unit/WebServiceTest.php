<?php

namespace Pronnect\GpWebPayTest\Unit;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\WebService;
use Psr\Log\LoggerInterface;
use SoapFault;

/**
 * Class WebServiceTest
 * @covers \Pronnect\GpWebPay\WebService
 */
class WebServiceTest extends TestCase
{
    private WebService $webService;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $wsdl = dirname(__DIR__, 2) . "/resources/wsdl/cws_v1.wsdl";
        $options = [
            'logger'          => $this->logger,
            'wsClientOptions' => [
                //'soap_version'   => SOAP_1_2,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'trace'      => true,
            ],
        ];
        $this->webService = new WebService($wsdl, $options);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(WebService::class, $this->webService);
    }

    public function testDoRequest(): void
    {
        $request = '<request>test</request>';
        $location = 'http://example.com';
        $action = 'testAction';
        $version = SOAP_1_1;

        $response = $this->webService->__doRequest($request, $location, $action, $version);

        $this->assertNull($response);
    }

    public function testConstructorWithSoapFault(): void
    {
        $this->expectException(SoapFault::class);

        $wsdl = 'invalid/wsdl';
        $options = ['logger' => $this->logger];
        new WebService($wsdl, $options);
    }
}
