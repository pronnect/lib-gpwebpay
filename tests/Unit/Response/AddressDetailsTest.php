<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\AddressDetails;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * @covers \Pronnect\GpWebPay\Response\AddressDetails
 * @covers \Pronnect\GpWebPay\Response\Response::__set
 */
class AddressDetailsTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetName(): void
    {
        $addressDetails = new AddressDetails();
        $reflection = new ReflectionClass($addressDetails);
        $property = $reflection->getProperty('name');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'John Doe');

        $this->assertSame('John Doe', $addressDetails->getName());
    }

    /**
     * @return void
     */
    public function testGetAddress1(): void
    {
        $addressDetails = new AddressDetails();
        $reflection = new ReflectionClass($addressDetails);
        $property = $reflection->getProperty('address1');
        $property->setAccessible(true);
        $property->setValue($addressDetails, '123 Main St');

        $this->assertSame('123 Main St', $addressDetails->getAddress1());
    }

    /**
     * @return void
     */
    public function testGetAddress2(): void
    {
        $addressDetails = new AddressDetails();
        $reflection = new ReflectionClass($addressDetails);
        $property = $reflection->getProperty('address2');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'Apt 4B');

        $this->assertSame('Apt 4B', $addressDetails->getAddress2());
    }

    /**
     * @return void
     */
    public function testGetAddress3(): void
    {
        $addressDetails = new AddressDetails();
        $reflection = new ReflectionClass($addressDetails);
        $property = $reflection->getProperty('address3');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'Suite 5');

        $this->assertSame('Suite 5', $addressDetails->getAddress3());
    }

    /**
     * @return void
     */
    public function testGetCity(): void
    {
        $addressDetails = new AddressDetails();
        $reflection = new ReflectionClass($addressDetails);
        $property = $reflection->getProperty('city');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'New York');

        $this->assertSame('New York', $addressDetails->getCity());
    }

    /**
     * @return void
     */
    public function testGetPostalCode(): void
    {
        $addressDetails = new AddressDetails();
        $reflection = new ReflectionClass($addressDetails);
        $property = $reflection->getProperty('postalCode');
        $property->setAccessible(true);
        $property->setValue($addressDetails, '10001');

        $this->assertSame('10001', $addressDetails->getPostalCode());
    }

    /**
     * @return void
     */
    public function testGetCountry(): void
    {
        $addressDetails = new AddressDetails();
        $reflection = new ReflectionClass($addressDetails);
        $property = $reflection->getProperty('country');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'USA');

        $this->assertSame('USA', $addressDetails->getCountry());
    }

    /**
     * @return void
     */
    public function testGetCountrySubdivision(): void
    {
        $addressDetails = new AddressDetails();
        $reflection = new ReflectionClass($addressDetails);
        $property = $reflection->getProperty('countrySubdivision');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'NY');

        $this->assertSame('NY', $addressDetails->getCountrySubdivision());
    }

    /**
     * @return void
     */
    public function testGetPhone(): void
    {
        $addressDetails = new AddressDetails();
        $reflection = new ReflectionClass($addressDetails);
        $property = $reflection->getProperty('phone');
        $property->setAccessible(true);
        $property->setValue($addressDetails, '1234567890');

        $this->assertSame('1234567890', $addressDetails->getPhone());
    }

    /**
     * @return void
     */
    public function testGetEmail(): void
    {
        $addressDetails = new AddressDetails();
        $reflection = new ReflectionClass($addressDetails);
        $property = $reflection->getProperty('email');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'john.doe@example.com');

        $this->assertSame('john.doe@example.com', $addressDetails->getEmail());
    }

    /**
     * @return void
     */
    public function testGetDigest(): void
    {
        $addressDetails = new AddressDetails();
        $reflection = new ReflectionClass($addressDetails);
        $property = $reflection->getProperty('name');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'John Doe');
        $property = $reflection->getProperty('address1');
        $property->setAccessible(true);
        $property->setValue($addressDetails, '123 Main St');
        $property = $reflection->getProperty('address2');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'Apt 4B');
        $property = $reflection->getProperty('address3');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'Suite 5');
        $property = $reflection->getProperty('city');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'New York');
        $property = $reflection->getProperty('postalCode');
        $property->setAccessible(true);
        $property->setValue($addressDetails, '10001');
        $property = $reflection->getProperty('country');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'USA');
        $property = $reflection->getProperty('countrySubdivision');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'NY');
        $property = $reflection->getProperty('phone');
        $property->setAccessible(true);
        $property->setValue($addressDetails, '1234567890');
        $property = $reflection->getProperty('email');
        $property->setAccessible(true);
        $property->setValue($addressDetails, 'john.doe@example.com');
        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            'John Doe',
            '123 Main St',
            'Apt 4B',
            'Suite 5',
            'New York',
            '10001',
            'USA',
            'NY',
            '1234567890',
            'john.doe@example.com',
        ]);
        $this->assertSame($expectedDigest, $addressDetails->getDigest());
    }
}
