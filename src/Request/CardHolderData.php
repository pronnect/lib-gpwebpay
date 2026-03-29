<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;

/**
 * Class CardHolderData
 *
 * 3DS2 cardholder data for SOAP requests. All sub-objects are optional.
 * Use CardholderDetails for identity/contact data, BillingDetails and
 * ShippingDetails for address data.
 */
class CardHolderData
{
    use DigestTrait;

    private ?CardholderDetails $cardholderDetails = null;
    private ?string $addressMatch = null;
    private ?BillingDetails $billingDetails = null;
    private ?ShippingDetails $shippingDetails = null;

    public function __get(string $name): mixed
    {
        return $this->$name ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->$name) && $this->$name !== null;
    }

    public function getCardholderDetails(): ?CardholderDetails
    {
        return $this->cardholderDetails;
    }

    public function setCardholderDetails(CardholderDetails $cardholderDetails): self
    {
        $this->cardholderDetails = $cardholderDetails;

        return $this;
    }

    /** Y = billing and shipping address match, N = they differ. */
    public function getAddressMatch(): ?string
    {
        return $this->addressMatch;
    }

    public function setAddressMatch(string $addressMatch): self
    {
        $this->addressMatch = $addressMatch;

        return $this;
    }

    public function getBillingDetails(): ?BillingDetails
    {
        return $this->billingDetails;
    }

    public function setBillingDetails(BillingDetails $billingDetails): self
    {
        $this->billingDetails = $billingDetails;

        return $this;
    }

    public function getShippingDetails(): ?ShippingDetails
    {
        return $this->shippingDetails;
    }

    public function setShippingDetails(ShippingDetails $shippingDetails): self
    {
        $this->shippingDetails = $shippingDetails;

        return $this;
    }

    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->cardholderDetails?->getDigest(),
            $this->addressMatch,
            $this->billingDetails?->getDigest(),
            $this->shippingDetails?->getDigest(),
        ]);
    }
}
