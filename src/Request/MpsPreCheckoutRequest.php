<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\RequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class MpsPreCheckoutRequest
 *
 * Uses merchantKey (provider + merchantNumber), no paymentNumber.
 */
class MpsPreCheckoutRequest implements RequestInterface, SignedInterface
{
    use RequestTrait;
    use DigestTrait;
    use SignedTrait;

    private ?string $pairingNumber = null;
    private ?bool $requestCardDetails = null;
    private ?bool $requestShippingDetails = null;
    private ?bool $requestRewardPrograms = null;

    public function getPairingNumber(): ?string
    {
        return $this->pairingNumber;
    }

    public function setPairingNumber(string $pairingNumber): self
    {
        $this->pairingNumber = $pairingNumber;

        return $this;
    }

    public function getRequestCardDetails(): ?bool
    {
        return $this->requestCardDetails;
    }

    public function setRequestCardDetails(bool $requestCardDetails): self
    {
        $this->requestCardDetails = $requestCardDetails;

        return $this;
    }

    public function getRequestShippingDetails(): ?bool
    {
        return $this->requestShippingDetails;
    }

    public function setRequestShippingDetails(bool $requestShippingDetails): self
    {
        $this->requestShippingDetails = $requestShippingDetails;

        return $this;
    }

    public function getRequestRewardPrograms(): ?bool
    {
        return $this->requestRewardPrograms;
    }

    public function setRequestRewardPrograms(bool $requestRewardPrograms): self
    {
        $this->requestRewardPrograms = $requestRewardPrograms;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->messageId ?? null,
            $this->provider ?? null,
            $this->merchantNumber ?? null,
            $this->pairingNumber ?? null,
        ]);
    }
}
