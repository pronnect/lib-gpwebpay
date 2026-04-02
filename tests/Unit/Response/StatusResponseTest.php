<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use Pronnect\GpWebPay\Response\StatusResponse;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * @covers \Pronnect\GpWebPay\Response\StatusResponse
 */
class StatusResponseTest extends ResponseTest
{
    /**
     * @covers \Pronnect\GpWebPay\Response\Response::__set
     * @return void
     */
    public function testGetStatus(): void
    {
        $response = new StatusResponse();
        $status = "some-status";
        $response->status = $status;
        $this->assertSame($status, $response->getStatus());
    }

    public function testGetDigests(): void
    {
        $response = new StatusResponse();
        $reflection = new ReflectionClass($response);

        $property = $reflection->getProperty('messageId');
        $property->setAccessible(true);
        $property->setValue($response, 'message123');

        $property = $reflection->getProperty('status');
        $property->setAccessible(true);
        $property->setValue($response, 'some-status');

        $expectedDigests = implode(DigestInterface::DIGEST_SEPARATOR, [
            'message123',
            'some-status',
        ]);

        $this->assertSame($expectedDigests, $response->getDigest());
    }
}
