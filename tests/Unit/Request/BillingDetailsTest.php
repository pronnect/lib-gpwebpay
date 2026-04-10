<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\BillingDetails;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\BillingDetails
 */
class BillingDetailsTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $d = new BillingDetails();
        $d->setName('John Doe')
            ->setAddress1('Main Street 1')
            ->setAddress2('Floor 2')
            ->setAddress3('Suite 300')
            ->setCity('Prague')
            ->setPostalCode('11000')
            ->setCountry('CZ')
            ->setCountrySubdivision('CZ-PR')
            ->setPhone('420123456789')
            ->setEmail('billing@example.com');

        $this->assertSame('John Doe', $d->getName());
        $this->assertSame('Main Street 1', $d->getAddress1());
        $this->assertSame('Floor 2', $d->getAddress2());
        $this->assertSame('Suite 300', $d->getAddress3());
        $this->assertSame('Prague', $d->getCity());
        $this->assertSame('11000', $d->getPostalCode());
        $this->assertSame('CZ', $d->getCountry());
        $this->assertSame('CZ-PR', $d->getCountrySubdivision());
        $this->assertSame('420123456789', $d->getPhone());
        $this->assertSame('billing@example.com', $d->getEmail());
    }

    public function testDefaultsAreNull(): void
    {
        $d = new BillingDetails();
        $this->assertNull($d->getName());
        $this->assertNull($d->getAddress2());
        $this->assertNull($d->getCountrySubdivision());
        $this->assertNull($d->getPhone());
        $this->assertNull($d->getEmail());
    }

    public function testGetDigestWithRequiredFields(): void
    {
        $d = new BillingDetails();
        $d->setName('John')
            ->setAddress1('Street 1')
            ->setCity('Prague')
            ->setPostalCode('11000')
            ->setCountry('CZ');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, ['John', 'Street 1', 'Prague', '11000', 'CZ']);
        $this->assertSame($expected, $d->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new BillingDetails())->getDigest());
    }
}
