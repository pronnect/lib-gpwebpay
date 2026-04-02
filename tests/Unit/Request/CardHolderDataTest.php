<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\BillingDetails;
use Pronnect\GpWebPay\Request\CardHolderData;
use Pronnect\GpWebPay\Request\CardholderDetails;
use Pronnect\GpWebPay\Request\ShippingDetails;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\CardHolderData
 * @uses \Pronnect\GpWebPay\Request\BillingDetails
 * @uses \Pronnect\GpWebPay\Request\CardholderDetails
 * @uses \Pronnect\GpWebPay\Request\ShippingDetails
 */
class CardHolderDataTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $data = new CardHolderData();
        $cardholderDetails = new CardholderDetails();
        $billingDetails    = new BillingDetails();
        $shippingDetails   = new ShippingDetails();

        $data->setCardholderDetails($cardholderDetails)
            ->setAddressMatch('Y')
            ->setBillingDetails($billingDetails)
            ->setShippingDetails($shippingDetails);

        $this->assertSame($cardholderDetails, $data->getCardholderDetails());
        $this->assertSame('Y', $data->getAddressMatch());
        $this->assertSame($billingDetails, $data->getBillingDetails());
        $this->assertSame($shippingDetails, $data->getShippingDetails());
    }

    public function testSettersReturnSelf(): void
    {
        $data = new CardHolderData();
        $this->assertSame($data, $data->setAddressMatch('N'));
        $this->assertSame($data, $data->setCardholderDetails(new CardholderDetails()));
        $this->assertSame($data, $data->setBillingDetails(new BillingDetails()));
        $this->assertSame($data, $data->setShippingDetails(new ShippingDetails()));
    }

    public function testDefaultsAreNull(): void
    {
        $data = new CardHolderData();
        $this->assertNull($data->getCardholderDetails());
        $this->assertNull($data->getAddressMatch());
        $this->assertNull($data->getBillingDetails());
        $this->assertNull($data->getShippingDetails());
    }

    public function testGetDigestContainsAddressMatch(): void
    {
        $data = new CardHolderData();
        $data->setAddressMatch('Y');

        $this->assertSame('Y', $data->getDigest());
    }

    public function testGetDigestIncludesSubObjectDigests(): void
    {
        $cardholderDetails = (new CardholderDetails())->setName('John')->setEmail('john@example.com');
        $billingDetails    = (new BillingDetails())->setName('John')->setAddress1('Str 1')->setCity('Prague')->setPostalCode('11000')->setCountry('CZ');

        $data = new CardHolderData();
        $data->setCardholderDetails($cardholderDetails)
            ->setAddressMatch('Y')
            ->setBillingDetails($billingDetails);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            $cardholderDetails->getDigest(),
            'Y',
            $billingDetails->getDigest(),
        ]);

        $this->assertSame($expected, $data->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new CardHolderData())->getDigest());
    }
}
