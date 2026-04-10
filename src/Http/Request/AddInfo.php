<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request;

use Pronnect\GpWebPay\Http\Exception\HttpRequestException;
use Pronnect\GpWebPay\Http\Request\AddInfo\CardholderInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\PaymentInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\ShoppingCartInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\WalletDetails;

/**
 * Builder for the ADDINFO XML parameter (PSD2 additional info, version 5).
 *
 * Spec: GPwebpayAdditionalInfoRequest_v.5.xsd
 *
 * The generated XML is validated against the bundled XSD before being returned.
 * toXml() throws HttpRequestException if the data violates the schema.
 *
 * ⚠️ CRITICAL: XML must have NO whitespace between tags.
 *   Browsers encode whitespace inconsistently when submitting GET forms,
 *   which causes the signature to fail. Always use POST when ADDINFO is set.
 *
 * ⚠️ ADDINFO requires POST form submission — HttpGateway::getRedirectUrl() will
 *   throw HttpRequestException if ADDINFO is present.
 *
 * Usage:
 *   $addInfo = (new AddInfo())
 *       ->setPaymentInfo((new PaymentInfo())->setTransactionType('01'))
 *       ->setShoppingCartInfo(
 *           (new ShoppingCartInfo())->addItem(new ShoppingCartItem('Product', 1, 19900))
 *       );
 *   $request->setAddInfo($addInfo->toXml());
 */
class AddInfo
{
    private const XSD_PATH = __DIR__ . '/../../../resources/wsdl/GPwebpayAdditionalInfoRequest_v.5.xsd';

    private ?CardholderInfo   $cardholderInfo   = null;
    private ?PaymentInfo      $paymentInfo      = null;
    private ?ShoppingCartInfo $shoppingCartInfo = null;
    private ?WalletDetails    $walletDetails    = null;

    public function setCardholderInfo(CardholderInfo $info): self
    {
        $this->cardholderInfo = $info;
        return $this;
    }

    public function setPaymentInfo(PaymentInfo $info): self
    {
        $this->paymentInfo = $info;
        return $this;
    }

    public function setShoppingCartInfo(ShoppingCartInfo $info): self
    {
        $this->shoppingCartInfo = $info;
        return $this;
    }

    public function setWalletDetails(WalletDetails $details): self
    {
        $this->walletDetails = $details;
        return $this;
    }

    /**
     * Renders the complete ADDINFO XML string and validates it against the XSD.
     *
     * @throws HttpRequestException if the XML does not conform to the XSD schema
     */
    public function toXml(): string
    {
        $content = '';

        if ($this->cardholderInfo !== null) {
            $xml = $this->cardholderInfo->toXml();
            if ($xml !== null) {
                $content .= $xml;
            }
        }

        if ($this->paymentInfo !== null) {
            $xml = $this->paymentInfo->toXml();
            if ($xml !== null) {
                $content .= $xml;
            }
        }

        if ($this->shoppingCartInfo !== null) {
            $xml = $this->shoppingCartInfo->toXml();
            if ($xml !== null) {
                $content .= $xml;
            }
        }

        if ($this->walletDetails !== null) {
            $xml = $this->walletDetails->toXml();
            if ($xml !== null) {
                $content .= $xml;
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<additionalInfoRequest xmlns="http://gpe.cz/gpwebpay/additionalInfo/request" version="5.0">'
            . $content
            . '</additionalInfoRequest>';

        $xml = trim(str_replace("\t", " ", str_replace("\r", "", str_replace("\n", " ", $xml))));

        $this->validateAgainstXsd($xml);

        return $xml;
    }

    private function validateAgainstXsd(string $xml): void
    {
        $prev = libxml_use_internal_errors(true);

        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        $valid  = $doc->schemaValidate(self::XSD_PATH);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        if (!$valid) {
            $messages = array_map(
                static fn(\LibXMLError $e) => trim($e->message),
                $errors,
            );
            throw new HttpRequestException(
                'AddInfo XML is not valid against XSD: ' . implode('; ', $messages),
            );
        }
    }
}
