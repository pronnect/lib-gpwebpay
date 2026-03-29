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
                            ->setName('Jan Novák')
                            ->setEmail('jan@example.cz')
                            ->setPhone('+420', '123456789'),
                    )
                    ->setBillingDetails(
                        (new BillingDetails())
                            ->setCity('Praha')
                            ->setCountry('CZ')
                            ->setAddress1('Václavské náměstí 1'),
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
                            ->setAddress1('Ulica <Testovacia> & "Prvá"'),
                    ),
            );

        $xml = $addInfo->toXml();

        $this->assertStringNotContainsString('<Testovacia>', $xml, 'Raw < must be escaped');
        $this->assertStringContainsString('&lt;', $xml);
        $this->assertStringContainsString('&amp;', $xml);
    }

    public function testCardholderPhoneRequiresBothCountryAndNumber(): void
    {
        // setPhone() sets both at once — no way to set only one
        $details = (new CardholderDetails())->setPhone('+421', '987654321');
        $xml     = $details->toXml();

        $this->assertStringContainsString('<mobilePhone>', $xml);
        $this->assertStringContainsString('<phoneCountry>+421</phoneCountry>', $xml);
        $this->assertStringContainsString('<phone>987654321</phone>', $xml);
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

        // Must be parseable as valid XML
        $doc = new \DOMDocument();
        $result = $doc->loadXML($xml);

        $this->assertTrue($result, 'toXml() must produce valid XML: ' . $xml);
    }

    // ── WalletDetails ─────────────────────────────────────────────────────────

    public function testWalletDetailsIncludedInAddInfo(): void
    {
        $addInfo = (new AddInfo())
            ->setWalletDetails(
                (new WalletDetails())
                    ->setPaymentData('base64payloadhere')
                    ->setType('GOOGLEPAY'),
            );

        $xml = $addInfo->toXml();

        $this->assertStringContainsString('<walletDetails>', $xml);
        $this->assertStringContainsString('<paymentData>base64payloadhere</paymentData>', $xml);
        $this->assertStringContainsString('<type>GOOGLEPAY</type>', $xml);
    }

    public function testEmptyWalletDetailsReturnsNull(): void
    {
        $this->assertNull((new WalletDetails())->toXml());
    }

    public function testWalletDetailsWithOnlyType(): void
    {
        $xml = (new WalletDetails())->setType('APPLEPAY')->toXml();

        $this->assertNotNull($xml);
        $this->assertStringContainsString('<type>APPLEPAY</type>', $xml);
        $this->assertStringNotContainsString('<paymentData>', $xml);
    }

    // ── ShippingDetails ───────────────────────────────────────────────────────

    public function testShippingDetailsIncludedInCardholderInfo(): void
    {
        $shipping = (new ShippingDetails())
            ->setCity('Bratislava')
            ->setCountry('SK')
            ->setAddress1('Hlavná 1')
            ->setPostalCode('81101');

        $xml = $shipping->toXml();

        $this->assertNotNull($xml);
        $this->assertStringContainsString('<shippingDetails>', $xml);
        $this->assertStringContainsString('<city>Bratislava</city>', $xml);
        $this->assertStringContainsString('<country>SK</country>', $xml);
        $this->assertStringContainsString('<address1>Hlavná 1</address1>', $xml);
        $this->assertStringContainsString('<postalCode>81101</postalCode>', $xml);
    }

    public function testEmptyShippingDetailsReturnsNull(): void
    {
        $this->assertNull((new ShippingDetails())->toXml());
    }

    public function testShippingDetailsAllFields(): void
    {
        $xml = (new ShippingDetails())
            ->setCity('Košice')
            ->setCountry('SK')
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
                            ->setCity('Žilina')
                            ->setCountry('SK'),
                    ),
            );

        $xml = $addInfo->toXml();

        $this->assertStringContainsString('<shippingDetails>', $xml);
        $this->assertStringContainsString('<city>Žilina</city>', $xml);
    }

    // ── BillingDetails additional fields ──────────────────────────────────────

    public function testBillingDetailsAllFields(): void
    {
        $xml = (new BillingDetails())
            ->setCity('Praha')
            ->setCountry('CZ')
            ->setAddress1('Václavské nám. 1')
            ->setAddress2('Přízemí')
            ->setAddress3('Kancelária 5')
            ->setPostalCode('11000')
            ->setCountrySubdivision('Prague')
            ->toXml();

        $this->assertNotNull($xml);
        $this->assertStringContainsString('<address2>Přízemí</address2>', $xml);
        $this->assertStringContainsString('<address3>Kancelária 5</address3>', $xml);
        $this->assertStringContainsString('<countrySubdivision>Prague</countrySubdivision>', $xml);
    }

    // ── CardholderDetails additional phones ───────────────────────────────────

    public function testCardholderDetailsHomeAndWorkPhone(): void
    {
        $details = (new CardholderDetails())
            ->setHomePhone('+420', '987000111')
            ->setWorkPhone('+421', '123000999');

        $xml = $details->toXml();

        $this->assertNotNull($xml);
        $this->assertStringContainsString('<homePhone>', $xml);
        $this->assertStringContainsString('<phoneCountry>+420</phoneCountry>', $xml);
        $this->assertStringContainsString('<phone>987000111</phone>', $xml);
        $this->assertStringContainsString('<workPhone>', $xml);
        $this->assertStringContainsString('<phoneCountry>+421</phoneCountry>', $xml);
        $this->assertStringContainsString('<phone>123000999</phone>', $xml);
    }

    // ── CardholderInfo null return ─────────────────────────────────────────────

    public function testEmptyCardholderInfoReturnsNull(): void
    {
        $this->assertNull((new CardholderInfo())->toXml());
    }

    public function testCardholderInfoWithEmptyChildrenReturnsNull(): void
    {
        // All children return null → cardholderInfo should also return null
        $info = (new CardholderInfo())
            ->setCardholderDetails(new CardholderDetails())  // empty → toXml() = null
            ->setBillingDetails(new BillingDetails())        // empty → toXml() = null
            ->setShippingDetails(new ShippingDetails());     // empty → toXml() = null

        $this->assertNull($info->toXml());
    }

    // ── PaymentInfo additional fields ────────────────────────────────────────

    public function testPaymentInfoAllFields(): void
    {
        $xml = (new PaymentInfo())
            ->setTransactionType('01')
            ->setRecurringExpiry('20261231')
            ->setRecurringFrequency('30')
            ->setInstallment('3')
            ->toXml();

        $this->assertNotNull($xml);
        $this->assertStringContainsString('<recurringExpiry>20261231</recurringExpiry>', $xml);
        $this->assertStringContainsString('<recurringFrequency>30</recurringFrequency>', $xml);
        $this->assertStringContainsString('<installment>3</installment>', $xml);
    }

    public function testEmptyPaymentInfoReturnsNull(): void
    {
        $this->assertNull((new PaymentInfo())->toXml());
    }

    // ── AddInfo walletDetails null path ──────────────────────────────────────

    public function testAddInfoWithEmptyWalletDetailsSkipsIt(): void
    {
        $addInfo = (new AddInfo())
            ->setWalletDetails(new WalletDetails()); // empty → toXml() = null

        $xml = $addInfo->toXml();

        $this->assertStringNotContainsString('<walletDetails>', $xml);
    }
}
