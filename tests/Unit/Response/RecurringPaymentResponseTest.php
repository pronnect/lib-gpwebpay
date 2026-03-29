<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\RecurringPaymentResponse;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * @covers \Pronnect\GpWebPay\Response\RecurringPaymentResponse
 */
class RecurringPaymentResponseTest extends TestCase
{
    private function makeResponse(array $props): RecurringPaymentResponse
    {
        $response = new RecurringPaymentResponse();
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
            'messageId'               => 'msg-1',
            'authCode'                => 'AUTH-001',
            'traceId'                 => 'TRACE-001',
            'authResponseCode'        => '00',
            'authRRN'                 => 'RRN-001',
            'paymentAccountReference' => 'PAR-001',
        ]);

        $this->assertSame('msg-1', $response->getMessageId());
        $this->assertSame('AUTH-001', $response->getAuthCode());
        $this->assertSame('TRACE-001', $response->getTraceId());
        $this->assertSame('00', $response->getAuthResponseCode());
        $this->assertSame('RRN-001', $response->getAuthRRN());
        $this->assertSame('PAR-001', $response->getPaymentAccountReference());
    }

    public function testGetDigestWithAllFields(): void
    {
        $response = $this->makeResponse([
            'messageId'               => 'msg-1',
            'authCode'                => 'AUTH-001',
            'traceId'                 => 'TRACE-001',
            'authResponseCode'        => '00',
            'authRRN'                 => 'RRN-001',
            'paymentAccountReference' => 'PAR-001',
        ]);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', 'AUTH-001', 'TRACE-001', '00', 'RRN-001', 'PAR-001',
        ]);

        $this->assertSame($expected, $response->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $response = $this->makeResponse([
            'messageId' => 'msg-1',
            'authCode'  => 'AUTH-001',
        ]);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', 'AUTH-001',
        ]);

        $this->assertSame($expected, $response->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new RecurringPaymentResponse())->getDigest());
    }
}