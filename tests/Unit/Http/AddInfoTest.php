<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Http;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Http\Exception\HttpRequestException;
use Pronnect\GpWebPay\Http\Request\AddInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\BillingDetails;
use Pronnect\GpWebPay\Http\Request\AddInfo\CardholderDetails;
use Pronnect\GpWebPay\Http\Request\AddInfo\CardholderInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\PaymentInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\ShippingDetails;
use Pronnect\GpWebPay\Http\Request\AddInfo\ShoppingCartInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\ShoppingCartItem;
use Pronnect\GpWebPay\Http\Request\AddInfo\WalletDetails;

/**
 * @covers \Pronnect\GpWebPay\Http\Request\AddInfo
 * @covers \Pronnect\GpWebPay\Http\Request\AddInfo\CardholderInfo
 * @covers \Pronnect\GpWebPay\Http\Request\AddInfo\CardholderDetails
 * @covers \Pronnect\GpWebPay\Http\Request\AddInfo\BillingDetails
 * @covers \Pronnect\GpWebPay\Http\Request\AddInfo\PaymentInfo
 * @covers \Pronnect\GpWebPay\Http\Request\AddInfo\ShippingDetails
 * @covers \Pronnect\GpWebPay\Http\Request\AddInfo\ShoppingCartInfo
 * @covers \Pronnect\GpWebPay\Http\Request\AddInfo\ShoppingCartItem
 * @covers \Pronnect\GpWebPay\Http\Request\AddInfo\WalletDetails
 */
class AddInfoTest extends TestCase
{
    /**
     * CRITICAL: No whitespace between XML tags — any whitespace breaks the DIGEST signature.
     * This test must always pass before deploying ADDINFO support.
     */
    public function testAddInfoXmlHasNoWhitespaceBetweenTags(): void
    {
        $addInfo = (new AddInfo())
            ->setCardholderInfo(
                (new CardholderInfo())
                    ->setCardholderDetails(
                        (new CardholderDetails())
                            ->setName('Jan Novak')           // ASCII only — cardholderNameValue
                            ->setEmail('jan@example.cz')
                            ->setMobilePhone('420', '123456789'),  // digits only, no +
                    )
                    ->setBillingDetails(
                        (new BillingDetails())
                            ->setName('Jan Novak')
                            ->setAddress1('Vaclavske namesti 1')
                            ->setCity('Praha')
                            ->setPostalCode('11000')
                            ->setCountry('203'),             // ISO 3166-1 numeric for CZ
                    ),
            )
            ->setPaymentInfo(
                (new PaymentInfo())->setTransactionType('01'),
            )
            ->setShoppingCartInfo(
                (new ShoppingCartInfo())->addItem(
                    new ShoppingCartItem('Produkt ABC', 2, 9950),
                ),
            );

        $xml = $addInfo->toXml();

        $this->assertDoesNotMatchRegularExpression(
            '/>\s+</',
            $xml,
            'AddInfo XML must NOT contain whitespace between tags — causes signature failure',
        );
    }

    public function testToXmlWrapsInAdditionalInfoRequestVersion5(): void
    {
        $addInfo = new AddInfo();
        $xml     = $addInfo->toXml();

        $this->assertStringContainsString('<additionalInfoRequest xmlns="http://gpe.cz/gpwebpay/additionalInfo/request" version="5.0"', $xml);
        $this->assertStringEndsWith('</additionalInfoRequest>', $xml);
    }

    public function testToXmlWithNoFieldsIsValid(): void
    {
        $xml = (new AddInfo())->toXml();

        $this->assertStringContainsString('<additionalInfoRequest xmlns="http://gpe.cz/gpwebpay/additionalInfo/request" version="5.0"', $xml);
        $this->assertStringContainsString('</additionalInfoRequest>', $xml);
    }

    public function testPaymentInfoIsIncluded(): void
    {
        $addInfo = (new AddInfo())
            ->setPaymentInfo(
                (new PaymentInfo())->setTransactionType('01'),
            );

        $xml = $addInfo->toXml();

        $this->assertStringContainsString('<paymentInfo>', $xml);
        $this->assertStringContainsString('<transactionType>01</transactionType>', $xml);
    }

    public function testShoppingCartWithSingleItem(): void
    {
        $cart = (new ShoppingCartInfo())
            ->addItem(new ShoppingCartItem('Test Product', 1, 19900));

        $addInfo = (new AddInfo())->setShoppingCartInfo($cart);
        $xml     = $addInfo->toXml();

        $this->assertStringContainsString('<shoppingCartInfo>', $xml);
        $this->assertStringContainsString('<shoppingCartItems>', $xml);
        $this->assertStringContainsString('<shoppingCartItem>', $xml);
        $this->assertStringContainsString('<itemDescription>Test Product</itemDescription>', $xml);
        $this->assertStringContainsString('<itemQuantity>1</itemQuantity>', $xml);
        $this->assertStringContainsString('<itemUnitPrice>19900</itemUnitPrice>', $xml);
    }

    public function testShoppingCartThrowsWhenOver40Items(): void
    {
        $this->expectException(HttpRequestException::class);

        $cart = new ShoppingCartInfo();
        for ($i = 0; $i <= 40; $i++) {  // 41 items
            $cart->addItem(new ShoppingCartItem("Item {$i}", 1, 100));
        }
    }

    public function testSpecialCharactersAreEscapedInXml(): void
    {
        $addInfo = (new AddInfo())
            ->setCardholderInfo(
                (new CardholderInfo())
                    ->setBillingDetails(
                        (new BillingDetails())
                            ->setName('Test User')
                            ->setAddress1('Ulica <Testovacia> & "Prva"')
                            ->setCity('Testovo')
                            ->setPostalCode('12345')
                            ->setCountry('703'),  // Slovakia
                    ),
            );

        $xml = $addInfo->toXml();

        $this->assertStringNotContainsString('<Testovacia>', $xml, 'Raw < must be escaped');
        $this->assertStringContainsString('&lt;', $xml);
        $this->assertStringContainsString('&amp;', $xml);
    }

    // ── CardholderDetails phones ──────────────────────────────────────────────

    public function testMobilePhoneGeneratesFlatElements(): void
    {
        $details = (new CardholderDetails())->setMobilePhone('421', '987654321');
        $xml     = $details->toXml();

        $this->assertStringContainsString('<mobilePhoneCountry>421</mobilePhoneCountry>', $xml);
        $this->assertStringContainsString('<mobilePhone>987654321</mobilePhone>', $xml);
        $this->assertStringNotContainsString('<mobilePhone><', $xml, 'mobilePhone must be a leaf, not a container');
    }

    public function testHomePhoneGeneratesFlatElements(): void
    {
        $details = (new CardholderDetails())->setPhone('420', '987000111');
        $xml     = $details->toXml();

        $this->assertStringContainsString('<phoneCountry>420</phoneCountry>', $xml);
        $this->assertStringContainsString('<phone>987000111</phone>', $xml);
    }

    public function testWorkPhoneGeneratesFlatElements(): void
    {
        $details = (new CardholderDetails())->setWorkPhone('421', '123000999');
        $xml     = $details->toXml();

        $this->assertStringContainsString('<workPhoneCountry>421</workPhoneCountry>', $xml);
        $this->assertStringContainsString('<workPhone>123000999</workPhone>', $xml);
    }

    public function testEmptyCardholderDetailsReturnsNull(): void
    {
        $details = new CardholderDetails();
        $this->assertNull($details->toXml());
    }

    public function testEmptyBillingDetailsReturnsNull(): void
    {
        $billing = new BillingDetails();
        $this->assertNull($billing->toXml());
    }

    public function testEmptyShoppingCartReturnsNull(): void
    {
        $cart = new ShoppingCartInfo();
        $this->assertNull($cart->toXml());
    }

    public function testXmlIsValidXml(): void
    {
        $addInfo = (new AddInfo())
            ->setPaymentInfo(
                (new PaymentInfo())->setTransactionType('01'),
            )
            ->setShoppingCartInfo(
                (new ShoppingCartInfo())->addItem(
                    new ShoppingCartItem('Test', 1, 100),
                ),
            );

        $xml = $addInfo->toXml();

        $doc = new \DOMDocument();
        $this->assertTrue($doc->loadXML($xml), 'toXml() must produce valid XML: ' . $xml);
    }

    public function testToXmlThrowsOnXsdViolation(): void
    {
        $this->expectException(HttpRequestException::class);
        $this->expectExceptionMessageMatches('/XSD/');

        // transactionType "99" is not a valid enum value per XSD
        (new AddInfo())
            ->setPaymentInfo((new PaymentInfo())->setTransactionType('99'))
            ->toXml();
    }

    // ── WalletDetails ─────────────────────────────────────────────────────────

    public function testWalletDetailsIncludedInAddInfo(): void
    {
        $addInfo = (new AddInfo())
            ->setWalletDetails(
                (new WalletDetails())
                    ->setRequestShippingDetails(true)
                    ->setRequestCardsDetails(false),
            );

        $xml = $addInfo->toXml();

        $this->assertStringContainsString('<walletDetails>', $xml);
        $this->assertStringContainsString('<requestShippingDetails>true</requestShippingDetails>', $xml);
        $this->assertStringContainsString('<requestCardsDetails>false</requestCardsDetails>', $xml);
    }

    public function testEmptyWalletDetailsReturnsNull(): void
    {
        $this->assertNull((new WalletDetails())->toXml());
    }

    public function testWalletDetailsWithSingleField(): void
    {
        $xml = (new WalletDetails())->setRequestLoyaltyProgram(true)->toXml();

        $this->assertNotNull($xml);
        $this->assertStringContainsString('<requestLoyaltyProgram>true</requestLoyaltyProgram>', $xml);
        $this->assertStringNotContainsString('<requestShippingDetails>', $xml);
    }

    public function testAddInfoWithEmptyWalletDetailsSkipsIt(): void
    {
        $addInfo = (new AddInfo())
            ->setWalletDetails(new WalletDetails()); // empty → toXml() = null

        $xml = $addInfo->toXml();

        $this->assertStringNotContainsString('<walletDetails>', $xml);
    }

    // ── ShippingDetails ───────────────────────────────────────────────────────

    public function testShippingDetailsIncludedInCardholderInfo(): void
    {
        $shipping = (new ShippingDetails())
            ->setName('Jan Novak')
            ->setAddress1('Hlavna 1')
            ->setCity('Bratislava')
            ->setPostalCode('81101')
            ->setCountry('703');  // Slovakia ISO numeric

        $xml = $shipping->toXml();

        $this->assertNotNull($xml);
        $this->assertStringContainsString('<shippingDetails>', $xml);
        $this->assertStringContainsString('<city>Bratislava</city>', $xml);
        $this->assertStringContainsString('<country>703</country>', $xml);
        $this->assertStringContainsString('<address1>Hlavna 1</address1>', $xml);
        $this->assertStringContainsString('<postalCode>81101</postalCode>', $xml);
    }

    public function testEmptyShippingDetailsReturnsNull(): void
    {
        $this->assertNull((new ShippingDetails())->toXml());
    }

    public function testShippingDetailsAllFields(): void
    {
        $xml = (new ShippingDetails())
            ->setName('Jan Novak')
            ->setCity('Kosice')
            ->setCountry('703')
            ->setAddress1('Ulica 1')
            ->setAddress2('Vchod B')
            ->setAddress3('3. poschodie')
            ->setPostalCode('04001')
            ->setCountrySubdivision('KE')
            ->setEmail('doprava@example.sk')
            ->toXml();

        $this->assertNotNull($xml);
        $this->assertStringContainsString('<address2>Vchod B</address2>', $xml);
        $this->assertStringContainsString('<address3>3. poschodie</address3>', $xml);
        $this->assertStringContainsString('<countrySubdivision>KE</countrySubdivision>', $xml);
        $this->assertStringContainsString('<email>doprava@example.sk</email>', $xml);
    }

    public function testShippingDetailsInCardholderInfoViaAddInfo(): void
    {
        $addInfo = (new AddInfo())
            ->setCardholderInfo(
                (new CardholderInfo())
                    ->setShippingDetails(
                        (new ShippingDetails())
                            ->setName('Jan Novak')
                            ->setAddress1('Hlavna 1')
                            ->setCity('Zilina')
                            ->setPostalCode('01001')
                            ->setCountry('703'),  // Slovakia ISO numeric
                    ),
            );

        $xml = $addInfo->toXml();

        $this->assertStringContainsString('<shippingDetails>', $xml);
        $this->assertStringContainsString('<city>Zilina</city>', $xml);
    }

    // ── BillingDetails additional fields ──────────────────────────────────────

    public function testBillingDetailsAllFields(): void
    {
        $xml = (new BillingDetails())
            ->setName('Jan Novak')
            ->setCity('Praha')
            ->setCountry('203')
            ->setAddress1('Vaclavske nam. 1')
            ->setAddress2('Prizemi')
            ->setAddress3('Kancelaria 5')
            ->setPostalCode('11000')
            ->setCountrySubdivision('PR')
            ->toXml();

        $this->assertNotNull($xml);
        $this->assertStringContainsString('<address2>Prizemi</address2>', $xml);
        $this->assertStringContainsString('<address3>Kancelaria 5</address3>', $xml);
        $this->assertStringContainsString('<countrySubdivision>PR</countrySubdivision>', $xml);
    }

    // ── CardholderInfo null return ─────────────────────────────────────────────

    public function testEmptyCardholderInfoReturnsNull(): void
    {
        $this->assertNull((new CardholderInfo())->toXml());
    }

    public function testCardholderInfoWithEmptyChildrenReturnsNull(): void
    {
        $info = (new CardholderInfo())
            ->setCardholderDetails(new CardholderDetails())  // empty → toXml() = null
            ->setBillingDetails(new BillingDetails())        // empty → toXml() = null
            ->setShippingDetails(new ShippingDetails());     // empty → toXml() = null

        $this->assertNull($info->toXml());
    }

    // ── PaymentInfo ──────────────────────────────────────────────────────────

    public function testPaymentInfoAllFields(): void
    {
        $xml = (new PaymentInfo())
            ->setTransactionType('01')
            ->setRecurringExpiry('20261231')
            ->setRecurringFrequency('30')
            ->toXml();

        $this->assertNotNull($xml);
        $this->assertStringContainsString('<recurringExpiry>20261231</recurringExpiry>', $xml);
        $this->assertStringContainsString('<recurringFrequency>30</recurringFrequency>', $xml);
    }

    public function testEmptyPaymentInfoReturnsNull(): void
    {
        $this->assertNull((new PaymentInfo())->toXml());
    }
}
