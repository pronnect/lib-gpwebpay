<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\Contact;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * @covers \Pronnect\GpWebPay\Response\Contact
 * @covers \Pronnect\GpWebPay\Response\Response::__set
 */
class ContactTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetFirstName(): void
    {
        $contact = new Contact();
        $reflection = new ReflectionClass($contact);
        $property = $reflection->getProperty('firstName');
        $property->setAccessible(true);
        $property->setValue($contact, 'John');

        $this->assertSame('John', $contact->getFirstName());
    }

    /**
     * @return void
     */
    public function testGetLastName(): void
    {
        $contact = new Contact();
        $reflection = new ReflectionClass($contact);
        $property = $reflection->getProperty('lastName');
        $property->setAccessible(true);
        $property->setValue($contact, 'Doe');

        $this->assertSame('Doe', $contact->getLastName());
    }

    /**
     * @return void
     */
    public function testGetCountry(): void
    {
        $contact = new Contact();
        $reflection = new ReflectionClass($contact);
        $property = $reflection->getProperty('country');
        $property->setAccessible(true);
        $property->setValue($contact, 'USA');

        $this->assertSame('USA', $contact->getCountry());
    }

    /**
     * @return void
     */
    public function testGetPhone(): void
    {
        $contact = new Contact();
        $reflection = new ReflectionClass($contact);
        $property = $reflection->getProperty('phone');
        $property->setAccessible(true);
        $property->setValue($contact, '1234567890');

        $this->assertSame('1234567890', $contact->getPhone());
    }

    /**
     * @return void
     */
    public function testGetEmail(): void
    {
        $contact = new Contact();
        $reflection = new ReflectionClass($contact);
        $property = $reflection->getProperty('email');
        $property->setAccessible(true);
        $property->setValue($contact, 'john.doe@example.com');

        $this->assertSame('john.doe@example.com', $contact->getEmail());
    }

    /**
     * @return void
     */
    public function testGetDigest(): void
    {
        $contact = new Contact();
        $reflection = new ReflectionClass($contact);
        $property = $reflection->getProperty('firstName');
        $property->setAccessible(true);
        $property->setValue($contact, 'John');
        $property = $reflection->getProperty('lastName');
        $property->setAccessible(true);
        $property->setValue($contact, 'Doe');
        $property = $reflection->getProperty('country');
        $property->setAccessible(true);
        $property->setValue($contact, 'USA');
        $property = $reflection->getProperty('phone');
        $property->setAccessible(true);
        $property->setValue($contact, '1234567890');
        $property = $reflection->getProperty('email');
        $property->setAccessible(true);
        $property->setValue($contact, 'john.doe@example.com');

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            'John',
            'Doe',
            'USA',
            '1234567890',
            'john.doe@example.com',
        ]);
        $this->assertSame($expectedDigest, $contact->getDigest());
    }
}
