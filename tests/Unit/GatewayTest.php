<?php

namespace Pronnect\GpWebPayTest\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Config;
use Pronnect\GpWebPay\DigestSigner;
use Pronnect\GpWebPay\Gateway;
use Pronnect\GpWebPay\Request\PaymentStatusRequest;
use Pronnect\GpWebPay\Response\CardOnFilePaymentFaultDetail;
use Pronnect\GpWebPay\Response\StateResponse;
use Pronnect\GpWebPay\ServiceProvider;
use Pronnect\GpWebPay\WebService;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\StateResponseInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use SoapFault;
use stdClass;

/**
 * Class GatewayTest
 * @covers \Pronnect\GpWebPay\Gateway
 */
class GatewayTest extends TestCase
{
    private Config $config;
    private string $signaturePrivateKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->signaturePrivateKey = file_get_contents(getenv('GPWEBPAY_PRIVATE_KEY'));
        $this->config = new Config([
            'isTestEnvironment'          => true,
            'GPEPublicKey'               => file_get_contents(getenv('GPWEBPAY_PUBLIC_KEY')),
            'merchantPrivateKey'         => file_get_contents(getenv('GPWEBPAY_MERCHANT_PRIVATE_KEY')),
            'merchantPrivateKeyPassword' => getenv('GPWEBPAY_MERCHANT_PRIVATE_KEY_PASSWORD') ?: null,
            'merchantNumber'             => '1234123',
            'provider'                   => ServiceProvider::PROVIDER_CSOB_SK,
            'wsClientOptions'            => [
                //'soap_version'   => SOAP_1_2,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'trace'      => true,
            ],
        ]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testEcho(): void
    {
        $mockedSoapClient = Mockery::mock('overload:' . WebService::class);
        $mockedSoapClient->shouldReceive('echo')->with(null)->once();
        $gateway = new Gateway(new Config([]), new DigestSigner("", "", ""), new NullLogger());
        $gateway->echo();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCallWS(): void
    {
        $paymentStatusRequest = new PaymentStatusRequest();
        $paymentStatusRequest->setPaymentNumber('314251');
        $paymentStatusResponse = new StateResponse();
        $paymentStatusResponse->messageId = '123';
        $paymentStatusResponse->status = 'OK';
        $paymentStatusResponse->state = 'OK';
        $paymentStatusResponse->subStatus = "";
        $paymentStatusResponse->setSignature($this->sign($paymentStatusResponse->getDigest()));
        $response = new stdClass;
        $response->paymentStatusResponse = $paymentStatusResponse;

        $mockedSoapClient = Mockery::mock('overload:' . WebService::class);

        $mockedSoapClient->shouldReceive('getPaymentStatus')
            //->withArgs(['paymentStatusRequest' => $paymentStatusRequest])
            ->withAnyArgs()
            ->once()
            ->andReturn($response);
        $gateway = new Gateway($this->config);
        $response = $gateway->getPaymentStatus($paymentStatusRequest);
        $this->assertInstanceOf(StateResponseInterface::class, $response);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCallWSWithSoapFault(): void
    {
        $this->expectException(SoapFault::class);
        $paymentStatusRequest = new PaymentStatusRequest();
        $paymentStatusRequest->setPaymentNumber('314251');
        $paymentStatusResponse = new StateResponse();
        $paymentStatusResponse->messageId = '123';
        $paymentStatusResponse->status = 'OK';
        $paymentStatusResponse->state = 'OK';
        $paymentStatusResponse->subStatus = "";
        $paymentStatusResponse->setSignature($this->sign($paymentStatusResponse->getDigest()));
        $serviceException = new stdClass;
        $serviceException->messageId = '123';
        $serviceException->primaryReturnCode = '0';
        $serviceException->secondaryReturnCode = '0';
        $serviceException->signature = base64_encode($this->sign(
            implode(
                DigestInterface::DIGEST_SEPARATOR, [
                    'messageId'           => $serviceException->messageId,
                    'primaryReturnCode'   => $serviceException->primaryReturnCode,
                    'secondaryReturnCode' => $serviceException->secondaryReturnCode,
                ]
            )
        ));

        $soapFault = new SoapFault('Server', 'Service exception', null, $serviceException);
        $soapFault->detail = new stdClass;
        $soapFault->detail->serviceException = $serviceException;
        $mockedSoapClient = Mockery::mock('overload:' . WebService::class);
        $mockedSoapClient->shouldReceive('getPaymentStatus')
            //->withArgs(['paymentStatusRequest' => $paymentStatusRequest])
            ->withAnyArgs()
            ->once()
            ->andThrows($soapFault);
        $gateway = new Gateway($this->config);
        $gateway->getPaymentStatus($paymentStatusRequest);
    }


    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCallWSWithCofFaultVerifiesSignature(): void
    {
        $this->expectException(SoapFault::class);

        $rawCof = new stdClass();
        $rawCof->messageId           = 'msg-cof';
        $rawCof->primaryReturnCode   = '46';
        $rawCof->secondaryReturnCode = '300';
        $rawCof->authenticationLink  = 'https://3dsecure.example.com/auth';
        $digest = implode(
            DigestInterface::DIGEST_SEPARATOR,
            ['msg-cof', '46', '300', 'https://3dsecure.example.com/auth'],
        );
        $rawCof->signature = base64_encode($this->sign($digest));

        $detail = new stdClass();
        $detail->cardOnFilePaymentServiceException = $rawCof;
        $soapFault = new SoapFault('Server', 'CoF 3DS required');
        $soapFault->detail = $detail;

        $mockedSoapClient = Mockery::mock('overload:' . WebService::class);
        $mockedSoapClient->shouldReceive('getPaymentStatus')->withAnyArgs()->once()->andThrows($soapFault);

        $gateway = new Gateway($this->config);
        $gateway->getPaymentStatus((new PaymentStatusRequest())->setPaymentNumber('314251'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCallWSWithCofFaultInvalidSignatureThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Response signature is not valid');

        $rawCof = new stdClass();
        $rawCof->messageId           = 'msg-cof';
        $rawCof->primaryReturnCode   = '46';
        $rawCof->secondaryReturnCode = '300';
        $rawCof->authenticationLink  = 'https://3dsecure.example.com/auth';
        $rawCof->signature           = base64_encode('invalid-signature');

        $detail = new stdClass();
        $detail->cardOnFilePaymentServiceException = $rawCof;
        $soapFault = new SoapFault('Server', 'CoF 3DS required');
        $soapFault->detail = $detail;

        $mockedSoapClient = Mockery::mock('overload:' . WebService::class);
        $mockedSoapClient->shouldReceive('getPaymentStatus')->withAnyArgs()->once()->andThrows($soapFault);

        $gateway = new Gateway($this->config);
        $gateway->getPaymentStatus((new PaymentStatusRequest())->setPaymentNumber('314251'));
    }

//
//    public function testCallWSWithInvalidSignature(): void
//    {
//        $this->expectException(RuntimeException::class);
//        $this->expectExceptionMessage('Response signature is not valid');
//
//        $request = $this->createMock(RequestInterface::class);
//        $response = $this->createMock(ResponseInterface::class);
//
//        $this->soapClient->expects($this->once())
//            ->method('__call')
//            ->with('someMethod', [['requestName' => $request]])
//            ->willReturn((object)['responseName' => $response]);
//
//        $this->digestSigner->expects($this->once())
//            ->method('sign')
//            ->with((string)$request->getDigest())
//            ->willReturn('signature');
//
//        $this->digestSigner->expects($this->once())
//            ->method('verify')
//            ->with((string)$response->getDigest(), (string)$response->getSignature())
//            ->willReturn(false);
//
//        $this->gateway->__call('someMethod', [$request]);
//    }

    public function tearDown(): void
    {
        parent::tearDown();
        $container = Mockery::getContainer();
        $this->addToAssertionCount($container->mockery_getExpectationCount());
        Mockery::close();
    }

    /**
     * @param string $value
     * @return void
     */
    private function sign(string $value): string
    {
        while (openssl_error_string() !== false) {
        }
        $primaryKey = openssl_pkey_get_private($this->signaturePrivateKey);
        $signature = "";
        openssl_sign($value, $signature, $primaryKey);
        while (openssl_error_string() !== false) {
        }
        return $signature;
    }
}
