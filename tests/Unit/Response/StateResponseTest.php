<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use Pronnect\GpWebPay\Response\LoyaltyProgramDetails;
use Pronnect\GpWebPay\Response\StateResponse;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;
use RuntimeException;

/**
 * @covers \Pronnect\GpWebPay\Response\StateResponse
 * @covers \Pronnect\GpWebPay\Response\StatusResponse
 * @covers \Pronnect\GpWebPay\Response\Response::__set
 */
class StateResponseTest extends StatusResponseTest
{
    /**
     * @covers \Pronnect\GpWebPay\Response\Response::__set
     */
    public function testGetState(): void
    {
        $response = new StateResponse();
        $state = "some-state";
        $response->state = $state;
        $this->assertSame($state, $response->getState());
    }

    /**
     * @covers \Pronnect\GpWebPay\Response\Response::__get
     */
    public function testGetNonExistingProperty(): void
    {
        $this->expectException(RuntimeException::class);
        $response = new StateResponse();
        $state = "some-state";
        $response->__set('state', $state);

        $this->expectExceptionMessage(
            sprintf(
                'Use class method "get%s" to get property "%s" instead of direct access',
                'State',
                'state'
            )
        );

        $response->__get('state');
    }

    /**
     * @covers \Pronnect\GpWebPay\Response\Response::__set
     */
    public function testSetExistingPropertyWithMethod(): void
    {
        $response = new StateResponse();
        $signature = "signature";
        $response->__set('signature', $signature);

        $this->assertSame($signature, $response->getSignature());
    }

    public function testGetDigests(): void
    {
        $response = new StateResponse();
        $reflection = new ReflectionClass($response);

        $property = $reflection->getProperty('messageId');
        $property->setAccessible(true);
        $property->setValue($response, 'message123');

        $property = $reflection->getProperty('status');
        $property->setAccessible(true);
        $property->setValue($response, 'some-status');

        $property = $reflection->getProperty('subStatus');
        $property->setAccessible(true);
        $property->setValue($response, 'some-sub-status');

        $property = $reflection->getProperty('state');
        $property->setAccessible(true);
        $property->setValue($response, 'some-state');

        $expectedDigests = implode(DigestInterface::DIGEST_SEPARATOR, [
            'message123',
            'some-state',
            'some-status',
            'some-sub-status'
        ]);

        $this->assertSame($expectedDigests, $response->getDigest());
    }
}
