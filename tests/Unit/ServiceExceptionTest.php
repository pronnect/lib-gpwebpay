<?php

namespace Pronnect\GpWebPayTest\Unit;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\ServiceException;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * Class ServiceExceptionTest
 * @covers \Pronnect\GpWebPay\ServiceException
 * @uses \Pronnect\GpWebPay\ReturnCodeResolver
 */
class ServiceExceptionTest extends TestCase
{
    public function testSetAndGetMessageId(): void
    {
        $exception = new ServiceException();
        $exception->setMessageId('12345');
        $this->assertSame('12345', $exception->getMessageId());
    }

    public function testSetAndGetPrimaryReturnCode(): void
    {
        $exception = new ServiceException();
        $exception->setPrimaryReturnCode('0');
        $this->assertSame('0', $exception->getPrimaryReturnCode());
    }

    public function testSetAndGetSecondaryReturnCode(): void
    {
        $exception = new ServiceException();
        $exception->setSecondaryReturnCode('002');
        $this->assertSame('002', $exception->getSecondaryReturnCode());
    }

    public function testGetDigest(): void
    {
        $exception = new ServiceException();
        $exception->setMessageId('12345');
        $exception->setPrimaryReturnCode('0');
        $exception->setSecondaryReturnCode('002');

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            '12345',
            '0',
            '002',
        ]);
        $this->assertSame($expectedDigest, $exception->getDigest());
    }
}
