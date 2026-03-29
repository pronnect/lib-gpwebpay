<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\LoyaltyProgramDetails;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * @covers \Pronnect\GpWebPay\Response\LoyaltyProgramDetails
 * @covers \Pronnect\GpWebPay\Response\Response::__set
 */
class LoyaltyProgramDetailsTest extends TestCase
{
    public function testGetProgramNumber(): void
    {
        $loyaltyProgramDetails = new LoyaltyProgramDetails();
        $reflection = new ReflectionClass($loyaltyProgramDetails);
        $property = $reflection->getProperty('programNumber');
        $property->setAccessible(true);
        $property->setValue($loyaltyProgramDetails, '123456');

        $this->assertSame('123456', $loyaltyProgramDetails->getProgramNumber());
    }

    public function testGetProgramId(): void
    {
        $loyaltyProgramDetails = new LoyaltyProgramDetails();
        $reflection = new ReflectionClass($loyaltyProgramDetails);
        $property = $reflection->getProperty('programId');
        $property->setAccessible(true);
        $property->setValue($loyaltyProgramDetails, 'program123');

        $this->assertSame('program123', $loyaltyProgramDetails->getProgramId());
    }

    public function testGetProgramName(): void
    {
        $loyaltyProgramDetails = new LoyaltyProgramDetails();
        $reflection = new ReflectionClass($loyaltyProgramDetails);
        $property = $reflection->getProperty('programName');
        $property->setAccessible(true);
        $property->setValue($loyaltyProgramDetails, 'Loyalty Program');

        $this->assertSame('Loyalty Program', $loyaltyProgramDetails->getProgramName());
    }

    public function testGetProgramExpiryMonth(): void
    {
        $loyaltyProgramDetails = new LoyaltyProgramDetails();
        $reflection = new ReflectionClass($loyaltyProgramDetails);
        $property = $reflection->getProperty('programExpiryMonth');
        $property->setAccessible(true);
        $property->setValue($loyaltyProgramDetails, '12');

        $this->assertSame('12', $loyaltyProgramDetails->getProgramExpiryMonth());
    }

    public function testGetProgramExpiryYear(): void
    {
        $loyaltyProgramDetails = new LoyaltyProgramDetails();
        $reflection = new ReflectionClass($loyaltyProgramDetails);
        $property = $reflection->getProperty('programExpiryYear');
        $property->setAccessible(true);
        $property->setValue($loyaltyProgramDetails, '2025');

        $this->assertSame('2025', $loyaltyProgramDetails->getProgramExpiryYear());
    }

    public function testGetDigest(): void
    {
        $loyaltyProgramDetails = new LoyaltyProgramDetails();
        $reflection = new ReflectionClass($loyaltyProgramDetails);
        $property = $reflection->getProperty('programNumber');
        $property->setAccessible(true);
        $property->setValue($loyaltyProgramDetails, '123456');
        $property = $reflection->getProperty('programId');
        $property->setAccessible(true);
        $property->setValue($loyaltyProgramDetails, 'program123');
        $property = $reflection->getProperty('programName');
        $property->setAccessible(true);
        $property->setValue($loyaltyProgramDetails, 'Loyalty Program');
        $property = $reflection->getProperty('programExpiryMonth');
        $property->setAccessible(true);
        $property->setValue($loyaltyProgramDetails, '12');
        $property = $reflection->getProperty('programExpiryYear');
        $property->setAccessible(true);
        $property->setValue($loyaltyProgramDetails, '2025');

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            '123456',
            'program123',
            'Loyalty Program',
            '12',
            '2025',
        ]);
        $this->assertSame($expectedDigest, $loyaltyProgramDetails->getDigest());
    }
}
