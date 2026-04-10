<?php declare(strict_types=1);

namespace Pronnect\GpWebPay\Tests\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\AdditionalInfoResponse;
use Pronnect\GpWebPay\Response\AddressDetails;
use Pronnect\GpWebPay\Response\CardDetail;
use Pronnect\GpWebPay\Response\Contact;
use Pronnect\GpWebPay\Response\LoyaltyProgramDetails;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * Class AdditionalInfoResponseTest
 * @covers \Pronnect\GpWebPay\Response\AdditionalInfoResponse
 * @covers \Pronnect\GpWebPay\Response\Response::__set
 */
class AdditionalInfoResponseTest extends TestCase
{
    public function testGetWalletDetails(): void
    {
        $response = new AdditionalInfoResponse();
        $reflection = new ReflectionClass($response);

        $property = $reflection->getProperty('walletDetails');
        $property->setAccessible(true);
        $property->setValue($response, 'wallet-details');

        $this->assertSame('wallet-details', $response->getWalletDetails());
    }

    public function testGetCardDetails(): void
    {
        $response = new AdditionalInfoResponse();
        $reflection = new ReflectionClass($response);

        $cardDetail = new CardDetail();
        $cardDetail->cardHolderName = 'John Doe';
        $cardDetail->expiryMonth = '12';
        $cardDetail->expiryYear = '2025';

        $property = $reflection->getProperty('cardDetails');
        $property->setAccessible(true);
        $property->setValue($response, [$cardDetail]);

        $this->assertSame([$cardDetail], $response->getCardDetails());
    }

    public function testGetDigest(): void
    {
        $response = new AdditionalInfoResponse();

        $contactMock = $this->createMock(Contact::class);
        $contactMock->method('getDigest')->willReturn('contact-digest');

        $billingDetailsMock = $this->createMock(AddressDetails::class);
        $billingDetailsMock->method('getDigest')->willReturn('billing-digest');

        $shippingDetailsMock = $this->createMock(AddressDetails::class);
        $shippingDetailsMock->method('getDigest')->willReturn('shipping-digest');

        $cardDetailMock = $this->createMock(CardDetail::class);
        $cardDetailMock->method('getDigest')->willReturn('card-digest');

        $loyaltyProgramDetailsMock = $this->createMock(LoyaltyProgramDetails::class);
        $loyaltyProgramDetailsMock->method('getDigest')->willReturn('loyalty-digest');

        $responseReflection = new ReflectionClass($response);
        $walletDetailsProperty = $responseReflection->getProperty('walletDetails');
        $walletDetailsProperty->setAccessible(true);
        $walletDetailsProperty->setValue($response, 'wallet-details');

        $contactProperty = $responseReflection->getProperty('contact');
        $contactProperty->setAccessible(true);
        $contactProperty->setValue($response, $contactMock);

        $billingDetailsProperty = $responseReflection->getProperty('billingDetails');
        $billingDetailsProperty->setAccessible(true);
        $billingDetailsProperty->setValue($response, $billingDetailsMock);

        $shippingDetailsProperty = $responseReflection->getProperty('shippingDetails');
        $shippingDetailsProperty->setAccessible(true);
        $shippingDetailsProperty->setValue($response, $shippingDetailsMock);

        $cardDetailsProperty = $responseReflection->getProperty('cardDetails');
        $cardDetailsProperty->setAccessible(true);
        $cardDetailsProperty->setValue($response, [$cardDetailMock]);

        $loyaltyProgramDetailsProperty = $responseReflection->getProperty('loyaltyProgramDetails');
        $loyaltyProgramDetailsProperty->setAccessible(true);
        $loyaltyProgramDetailsProperty->setValue($response, $loyaltyProgramDetailsMock);

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            'wallet-details',
            'contact-digest',
            'billing-digest',
            'shipping-digest',
            'card-digest',
            'loyalty-digest',
        ]);
        $this->assertSame($expectedDigest, $response->getDigest());
    }

    /**
     * @covers \Pronnect\GpWebPay\Response\Contact::getDigest
     * @return void
     */
    public function testGetDigestWithContact(): void
    {
        $response = new AdditionalInfoResponse();
        $contact = new Contact();
        $contact->firstName = 'John';
        $contact->lastName = 'Doe';
        $contact->email = 'john.doe@example.com';

        $responseReflection = new ReflectionClass($response);
        $contactProperty = $responseReflection->getProperty('walletDetails');
        $contactProperty->setAccessible(true);
        $contactProperty->setValue($response, "wallet-details");

        $contactProperty = $responseReflection->getProperty('contact');
        $contactProperty->setAccessible(true);
        $contactProperty->setValue($response, $contact);

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            'wallet-details',
            'John',
            'Doe',
            'john.doe@example.com',
        ]);
        $this->assertSame($expectedDigest, $response->getDigest());
    }

    /**
     * @covers \Pronnect\GpWebPay\Response\AddressDetails::getDigest
     * @return void
     */
    public function testGetDigestWithBillingAddress(): void
    {
        $response = new AdditionalInfoResponse();
        $billingAddress = new AddressDetails();
        $billingAddress->address1 = '123 Main St';
        $billingAddress->city = 'Anytown';
        $billingAddress->postalCode = '12345';
        $billingAddress->country = 'USA';

        $responseReflection = new ReflectionClass($response);
        $walletDetailsProperty = $responseReflection->getProperty('walletDetails');
        $walletDetailsProperty->setAccessible(true);
        $walletDetailsProperty->setValue($response, "wallet-details");

        $billingDetailsProperty = $responseReflection->getProperty('billingDetails');
        $billingDetailsProperty->setAccessible(true);
        $billingDetailsProperty->setValue($response, $billingAddress);

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            'wallet-details',
            '123 Main St',
            'Anytown',
            '12345',
            'USA',
        ]);
        $this->assertSame($expectedDigest, $response->getDigest());
    }

    /**
     * @covers \Pronnect\GpWebPay\Response\AddressDetails::getDigest
     * @return void
     */
    public function testGetDigestWithShippingDetails(): void
    {
        $response = new AdditionalInfoResponse();
        $shippingAddress = new AddressDetails();
        $shippingAddress->address1 = '456 Elm St';
        $shippingAddress->city = 'Othertown';
        $shippingAddress->postalCode = '67890';
        $shippingAddress->country = 'Canada';

        $responseReflection = new ReflectionClass($response);
        $walletDetailsProperty = $responseReflection->getProperty('walletDetails');
        $walletDetailsProperty->setAccessible(true);
        $walletDetailsProperty->setValue($response, "wallet-details");

        $shippingDetailsProperty = $responseReflection->getProperty('shippingDetails');
        $shippingDetailsProperty->setAccessible(true);
        $shippingDetailsProperty->setValue($response, $shippingAddress);

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            'wallet-details',
            '456 Elm St',
            'Othertown',
            '67890',
            'Canada',
        ]);
        $this->assertSame($expectedDigest, $response->getDigest());
    }

    /**
     * @covers \Pronnect\GpWebPay\Response\CardDetail::getDigest
     * @return void
     */
    public function testGetDigestWithCardDetails(): void
    {
        $response = new AdditionalInfoResponse();
        $cardDetail = new CardDetail();
        $cardDetail->cardHolderName = 'John Doe';
        $cardDetail->expiryMonth = '12';
        $cardDetail->expiryYear = '2025';

        $responseReflection = new ReflectionClass($response);
        $walletDetailsProperty = $responseReflection->getProperty('walletDetails');
        $walletDetailsProperty->setAccessible(true);
        $walletDetailsProperty->setValue($response, "wallet-details");

        $cardDetailsProperty = $responseReflection->getProperty('cardDetails');
        $cardDetailsProperty->setAccessible(true);
        $cardDetailsProperty->setValue($response, [$cardDetail]);

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            'wallet-details',
            'John Doe',
            '12',
            '2025',
        ]);
        $this->assertSame($expectedDigest, $response->getDigest());
    }

    /**
     * @covers \Pronnect\GpWebPay\Response\LoyaltyProgramDetails::getDigest
     * @return void
     */
    public function testGetDigestWithLoyaltyProgramDetails(): void
    {
        $response = new AdditionalInfoResponse();
        $loyaltyProgramDetails = new LoyaltyProgramDetails();
        $loyaltyProgramDetails->programNumber = '23452345';
        $loyaltyProgramDetails->programId = '1000';

        $responseReflection = new ReflectionClass($response);
        $walletDetailsProperty = $responseReflection->getProperty('walletDetails');
        $walletDetailsProperty->setAccessible(true);
        $walletDetailsProperty->setValue($response, "wallet-details");

        $loyaltyProgramDetailsProperty = $responseReflection->getProperty('loyaltyProgramDetails');
        $loyaltyProgramDetailsProperty->setAccessible(true);
        $loyaltyProgramDetailsProperty->setValue($response, $loyaltyProgramDetails);

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            'wallet-details',
            '23452345',
            '1000',
        ]);
        $this->assertSame($expectedDigest, $response->getDigest());
    }
}
