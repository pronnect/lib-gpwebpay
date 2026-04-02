<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\CardOnFilePaymentResponse;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * @covers \Pronnect\GpWebPay\Response\CardOnFilePaymentResponse
 */
class CardOnFilePaymentResponseTest extends TestCase
{
    private function makeResponse(array $props): CardOnFilePaymentResponse
    {
        $response = new CardOnFilePaymentResponse();
        $reflection = new ReflectionClass($response);
        foreach ($props as $name => $value) {
            $property = $reflection->getProperty($name);
            $property->setAccessible(true);
            $property->setValue($response, $value);
        }
        return $response;
    }

    public function testGetters(): void
    {
        $response = $this->makeResponse([
            'messageId'              => 'msg-10',
            'authCode'               => 'AUTH01',
            'tokenData'              => 'tok-resp',
            'traceId'                => 'trace-99',
            'authResponseCode'       => '00',
            'authRRN'                => 'rrn-001',
            'paymentAccountReference' => 'PAR-001',
        ]);

        $this->assertSame('msg-10', $response->getMessageId());
        $this->assertSame('AUTH01', $response->getAuthCode());
        $this->assertSame('tok-resp', $response->getTokenData());
        $this->assertSame('trace-99', $response->getTraceId());
        $this->assertSame('00', $response->getAuthResponseCode());
        $this->assertSame('rrn-001', $response->getAuthRRN());
        $this->assertSame('PAR-001', $response->getPaymentAccountReference());
    }

    public function testGetDigest(): void
    {
        $response = $this->makeResponse([
            'messageId'              => 'msg-10',
            'authCode'               => 'AUTH01',
            'tokenData'              => 'tok-resp',
            'traceId'                => 'trace-99',
            'authResponseCode'       => '00',
            'authRRN'                => 'rrn-001',
            'paymentAccountReference' => 'PAR-001',
        ]);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-10',
            'AUTH01',
            'tok-resp',
            'trace-99',
            '00',
            'rrn-001',
            'PAR-001',
        ]);

        $this->assertSame($expected, $response->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $response = $this->makeResponse([
            'messageId' => 'msg-11',
            'authCode'  => 'AUTH02',
        ]);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-11',
            'AUTH02',
        ]);

        $this->assertSame($expected, $response->getDigest());
    }
}
