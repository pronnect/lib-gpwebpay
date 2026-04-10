<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\Response;
use RuntimeException;


/**
 * @covers \Pronnect\GpWebPay\Response\Response
 */
class ResponseTest extends TestCase
{
    /**
     * @covers \Pronnect\GpWebPay\Response\Response::__set
     */
    public function testSetNonExistingProperty(): void
    {
        $this->expectException(RuntimeException::class);
        $response = $this->getMockForAbstractClass(Response::class);
        $this->expectExceptionMessage(
            sprintf('Class "%s" does not have property "nonExistingProperty"', get_class($response))
        );

        $response->__set('nonExistingProperty', 'value');
    }

    /**
     * @covers \Pronnect\GpWebPay\Response\Response::__get
     */
    public function testGetNonExistingProperty(): void
    {
        $this->expectException(RuntimeException::class);
        $response = $this->getMockForAbstractClass(Response::class);
        $this->expectExceptionMessage(
            sprintf('Class "%s" does not have property "%s"', get_class($response), 'nonExistingProperty')
        );

        $response->__get('nonExistingProperty');
    }

    /**
     * @covers \Pronnect\GpWebPay\Response\Response::__isset
     */
    public function testIssetNonExistingProperty(): void
    {
        $response = $this->getMockForAbstractClass(Response::class);

       $this->assertFalse($response->__isset('nonExistingProperty'));
    }
}
