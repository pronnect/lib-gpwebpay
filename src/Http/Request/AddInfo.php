<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request;

use Pronnect\GpWebPay\Http\Request\AddInfo\CardholderInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\PaymentInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\ShoppingCartInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\WalletDetails;

/**
 * Builder for the ADDINFO XML parameter (PSD2 additional info, version 5).
 *
 * Spec: GP Webpay HTTP API, kap. 10.3 (Príloha 3), version 5.0.
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
     * Renders the complete ADDINFO XML string.
     *
     * ⚠️ No whitespace or newlines between tags — any whitespace breaks the signature.
     *
     * @return string  Complete XML starting with <additionalInfoRequest version="5.0">
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

        return '<additionalInfoRequest version="5.0">' . $content . '</additionalInfoRequest>';
    }
}
