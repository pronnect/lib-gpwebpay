<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\RequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class CardDataRequest
 *
 * registrationKey is a CHOICE — only one of tokenData or masterPaymentNumber should be set.
 */
class CardDataRequest implements RequestInterface, SignedInterface
{
    use RequestTrait;
    use DigestTrait;
    use SignedTrait;

    private ?string $tokenData = null;
    private ?string $masterPaymentNumber = null;

    /**
     * @return string|null
     */
    public function getTokenData(): ?string
    {
        return $this->tokenData;
    }

    /**
     * @param string $tokenData
     *
     * @return $this
     */
    public function setTokenData(string $tokenData): self
    {
        $this->tokenData = $tokenData;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMasterPaymentNumber(): ?string
    {
        return $this->masterPaymentNumber;
    }

    /**
     * @param string $masterPaymentNumber
     *
     * @return $this
     */
    public function setMasterPaymentNumber(string $masterPaymentNumber): self
    {
        $this->masterPaymentNumber = $masterPaymentNumber;

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
            $this->masterPaymentNumber ?? null,
            $this->tokenData ?? null,
        ]);
    }
}
