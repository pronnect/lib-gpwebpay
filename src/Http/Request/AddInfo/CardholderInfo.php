<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request\AddInfo;

/**
 * Cardholder information container for ADDINFO v5.
 * Spec: kap. 10.3, element cardholderInfo.
 */
class CardholderInfo
{
    private ?CardholderDetails $cardholderDetails = null;
    private ?BillingDetails    $billingDetails    = null;
    private ?ShippingDetails   $shippingDetails   = null;

    public function setCardholderDetails(CardholderDetails $details): self
    {
        $this->cardholderDetails = $details;
        return $this;
    }

    public function setBillingDetails(BillingDetails $details): self
    {
        $this->billingDetails = $details;
        return $this;
    }

    public function setShippingDetails(ShippingDetails $details): self
    {
        $this->shippingDetails = $details;
        return $this;
    }

    public function toXml(): ?string
    {
        $content = '';

        if ($this->cardholderDetails !== null) {
            $xml = $this->cardholderDetails->toXml();
            if ($xml !== null) {
                $content .= $xml;
            }
        }
        if ($this->billingDetails !== null) {
            $xml = $this->billingDetails->toXml();
            if ($xml !== null) {
                $content .= $xml;
            }
        }
        if ($this->shippingDetails !== null) {
            $xml = $this->shippingDetails->toXml();
            if ($xml !== null) {
                $content .= $xml;
            }
        }

        return $content !== '' ? '<cardholderInfo>' . $content . '</cardholderInfo>' : null;
    }
}
