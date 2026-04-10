<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request\AddInfo;

/**
 * Wallet-level preferences for ADDINFO v5.
 * Spec: GPwebpayAdditionalInfoRequest_v.5.xsd, element walletDetails.
 */
class WalletDetails
{
    private ?bool   $requestShippingDetails     = null;
    private ?bool   $requestLoyaltyProgram      = null;
    private ?string $shippingLocationRestriction = null;
    private ?bool   $requestDeferredAuthorization = null;
    private ?bool   $requestCardsDetails         = null;

    public function setRequestShippingDetails(bool $value): self
    {
        $this->requestShippingDetails = $value;
        return $this;
    }

    public function setRequestLoyaltyProgram(bool $value): self
    {
        $this->requestLoyaltyProgram = $value;
        return $this;
    }

    /** 2-character word code (e.g. "US"). */
    public function setShippingLocationRestriction(string $value): self
    {
        $this->shippingLocationRestriction = $value;
        return $this;
    }

    public function setRequestDeferredAuthorization(bool $value): self
    {
        $this->requestDeferredAuthorization = $value;
        return $this;
    }

    public function setRequestCardsDetails(bool $value): self
    {
        $this->requestCardsDetails = $value;
        return $this;
    }

    public function toXml(): ?string
    {
        $content = '';

        if ($this->requestShippingDetails !== null) {
            $content .= '<requestShippingDetails>' . ($this->requestShippingDetails ? 'true' : 'false') . '</requestShippingDetails>';
        }
        if ($this->requestLoyaltyProgram !== null) {
            $content .= '<requestLoyaltyProgram>' . ($this->requestLoyaltyProgram ? 'true' : 'false') . '</requestLoyaltyProgram>';
        }
        if ($this->shippingLocationRestriction !== null) {
            $content .= '<shippingLocationRestriction>' . htmlspecialchars($this->shippingLocationRestriction, ENT_XML1, 'UTF-8') . '</shippingLocationRestriction>';
        }
        if ($this->requestDeferredAuthorization !== null) {
            $content .= '<requestDeferredAuthorization>' . ($this->requestDeferredAuthorization ? 'true' : 'false') . '</requestDeferredAuthorization>';
        }
        if ($this->requestCardsDetails !== null) {
            $content .= '<requestCardsDetails>' . ($this->requestCardsDetails ? 'true' : 'false') . '</requestCardsDetails>';
        }

        return $content !== '' ? '<walletDetails>' . $content . '</walletDetails>' : null;
    }
}
