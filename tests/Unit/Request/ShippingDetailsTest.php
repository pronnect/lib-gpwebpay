<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\ShippingDetails;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\ShippingDetails
 */
class ShippingDetailsTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $d = new ShippingDetails();
        $d->setName('Jane Doe')
            ->setAddress1('Delivery Rd 5')
            ->setAddress2('Apt 10')
            ->setAddress3('Block B')
            ->setCity('Bratislava')
            ->setPostalCode('81101')
            ->setCountry('SK')
            ->setCountrySubdivision('SK-BL')
            ->setPhone('421900123456')
            ->setEmail('ship@example.com')
            ->setMethod('SHIP_TO_BILLING_ADDRESS');

        $this->assertSame('Jane Doe', $d->getName());
        $this->assertSame('Delivery Rd 5', $d->getAddress1());
        $this->assertSame('Apt 10', $d->getAddress2());
        $this->assertSame('Block B', $d->getAddress3());
        $this->assertSame('Bratislava', $d->getCity());
        $this->assertSame('81101', $d->getPostalCode());
        $this->assertSame('SK', $d->getCountry());
        $this->assertSame('SK-BL', $d->getCountrySubdivision());
        $this->assertSame('421900123456', $d->getPhone());
        $this->assertSame('ship@example.com', $d->getEmail());
        $this->assertSame('SHIP_TO_BILLING_ADDRESS', $d->getMethod());
    }

    public function testDefaultsAreNull(): void
    {
        $d = new ShippingDetails();
        $this->assertNull($d->getName());
        $this->assertNull($d->getAddress2());
        $this->assertNull($d->getMethod());
    }

    public function testGetDigestWithRequiredFields(): void
    {
        $d = new ShippingDetails();
        $d->setName('Jane')
            ->setAddress1('Street 1')
            ->setCity('Bratislava')
            ->setPostalCode('81101')
            ->setCountry('SK');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, ['Jane', 'Street 1', 'Bratislava', '81101', 'SK']);
        $this->assertSame($expected, $d->getDigest());
    }

    public function testGetDigestIncludesMethodWhenSet(): void
    {
        $d = new ShippingDetails();
        $d->setName('Jane')
            ->setAddress1('Street 1')
            ->setCity('Bratislava')
            ->setPostalCode('81101')
            ->setCountry('SK')
            ->setMethod('01');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, ['Jane', 'Street 1', 'Bratislava', '81101', 'SK', '01']);
        $this->assertSame($expected, $d->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new ShippingDetails())->getDigest());
    }
}
