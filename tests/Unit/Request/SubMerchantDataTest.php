<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\SubMerchantData;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\SubMerchantData
 */
class SubMerchantDataTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $data = new SubMerchantData();
        $data->setMerchantId('SUBID123')
            ->setMerchantType('5411')
            ->setMerchantName('My Shop')
            ->setMerchantStreet('Main Street 1')
            ->setMerchantCity('Prague')
            ->setMerchantPostalCode('11000')
            ->setMerchantState('CZ')
            ->setMerchantCountry('CZ')
            ->setMerchantWeb('www.myshop.cz')
            ->setMerchantServiceNumber('420800123456')
            ->setMerchantMcAssignedId('MC123')
            ->setMerchantCountryOfOrigin('203');

        $this->assertSame('SUBID123', $data->getMerchantId());
        $this->assertSame('5411', $data->getMerchantType());
        $this->assertSame('My Shop', $data->getMerchantName());
        $this->assertSame('Main Street 1', $data->getMerchantStreet());
        $this->assertSame('Prague', $data->getMerchantCity());
        $this->assertSame('11000', $data->getMerchantPostalCode());
        $this->assertSame('CZ', $data->getMerchantState());
        $this->assertSame('CZ', $data->getMerchantCountry());
        $this->assertSame('www.myshop.cz', $data->getMerchantWeb());
        $this->assertSame('420800123456', $data->getMerchantServiceNumber());
        $this->assertSame('MC123', $data->getMerchantMcAssignedId());
        $this->assertSame('203', $data->getMerchantCountryOfOrigin());
    }

    public function testSettersReturnSelf(): void
    {
        $data = new SubMerchantData();
        $this->assertSame($data, $data->setMerchantId('X'));
        $this->assertSame($data, $data->setMerchantType('5411'));
        $this->assertSame($data, $data->setMerchantCountry('SK'));
    }

    public function testDefaultsAreNull(): void
    {
        $data = new SubMerchantData();
        $this->assertNull($data->getMerchantId());
        $this->assertNull($data->getMerchantState());
        $this->assertNull($data->getMerchantMcAssignedId());
        $this->assertNull($data->getMerchantCountryOfOrigin());
    }

    public function testGetDigestWithRequiredFields(): void
    {
        $data = new SubMerchantData();
        $data->setMerchantId('SUB1')
            ->setMerchantType('5411')
            ->setMerchantName('Shop')
            ->setMerchantStreet('Street')
            ->setMerchantCity('City')
            ->setMerchantPostalCode('10000')
            ->setMerchantCountry('CZ')
            ->setMerchantWeb('www.shop.cz')
            ->setMerchantServiceNumber('123456789');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'SUB1', '5411', 'Shop', 'Street', 'City', '10000', 'CZ', 'www.shop.cz', '123456789',
        ]);

        $this->assertSame($expected, $data->getDigest());
    }

    public function testGetDigestIncludesOptionalFieldsWhenSet(): void
    {
        $data = new SubMerchantData();
        $data->setMerchantId('SUB1')
            ->setMerchantType('5411')
            ->setMerchantName('Shop')
            ->setMerchantStreet('Street')
            ->setMerchantCity('City')
            ->setMerchantPostalCode('10000')
            ->setMerchantState('SK')
            ->setMerchantCountry('SK')
            ->setMerchantWeb('www.shop.sk')
            ->setMerchantServiceNumber('123')
            ->setMerchantMcAssignedId('MC1')
            ->setMerchantCountryOfOrigin('703');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'SUB1', '5411', 'Shop', 'Street', 'City', '10000', 'SK', 'SK', 'www.shop.sk', '123', 'MC1', '703',
        ]);

        $this->assertSame($expected, $data->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new SubMerchantData())->getDigest());
    }
}
