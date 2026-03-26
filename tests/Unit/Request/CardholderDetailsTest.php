<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\CardholderDetails;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\CardholderDetails
 */
class CardholderDetailsTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $d = new CardholderDetails();
        $d->setName('John Doe')
            ->setEmail('john@example.com')
            ->setLoginId('login123')
            ->setLoginType('02')
            ->setLoginTime('202601011200')
            ->setUserAccountId('acc-1')
            ->setUserAccountCreatedDate('20250101')
            ->setUserAccountAge('04')
            ->setUserAccountLastChangeDate('20260101')
            ->setUserAccountLastChangeAge('03')
            ->setUserAccountPasswordChangeDate('20260101')
            ->setUserAccountPasswordChangeAge('02')
            ->setSocialNetworkId('sn-1')
            ->setPhoneCountry('421')
            ->setPhone('900123456')
            ->setMobilePhoneCountry('421')
            ->setMobilePhone('900123456')
            ->setWorkPhoneCountry('421')
            ->setWorkPhone('200123456')
            ->setClientIpAddress('192.168.1.1');

        $this->assertSame('John Doe', $d->getName());
        $this->assertSame('john@example.com', $d->getEmail());
        $this->assertSame('login123', $d->getLoginId());
        $this->assertSame('02', $d->getLoginType());
        $this->assertSame('acc-1', $d->getUserAccountId());
        $this->assertSame('04', $d->getUserAccountAge());
        $this->assertSame('03', $d->getUserAccountLastChangeAge());
        $this->assertSame('02', $d->getUserAccountPasswordChangeAge());
        $this->assertSame('sn-1', $d->getSocialNetworkId());
        $this->assertSame('421', $d->getPhoneCountry());
        $this->assertSame('900123456', $d->getPhone());
        $this->assertSame('421', $d->getMobilePhoneCountry());
        $this->assertSame('900123456', $d->getMobilePhone());
        $this->assertSame('421', $d->getWorkPhoneCountry());
        $this->assertSame('200123456', $d->getWorkPhone());
        $this->assertSame('192.168.1.1', $d->getClientIpAddress());
    }

    public function testDefaultsAreNull(): void
    {
        $d = new CardholderDetails();
        $this->assertNull($d->getName());
        $this->assertNull($d->getEmail());
        $this->assertNull($d->getLoginId());
        $this->assertNull($d->getClientIpAddress());
    }

    public function testGetDigestWithRequiredFields(): void
    {
        $d = new CardholderDetails();
        $d->setName('Jane')->setEmail('jane@example.com');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, ['Jane', 'jane@example.com']);
        $this->assertSame($expected, $d->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $d = new CardholderDetails();
        $d->setName('Jane')->setEmail('jane@example.com')->setPhone('123456789');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, ['Jane', 'jane@example.com', '123456789']);
        $this->assertSame($expected, $d->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new CardholderDetails())->getDigest());
    }
}
