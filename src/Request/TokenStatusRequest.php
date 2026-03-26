<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\RequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class TokenStatusRequest
 */
class TokenStatusRequest implements RequestInterface, SignedInterface
{
    use RequestTrait;
    use DigestTrait;
    use SignedTrait;

    private ?string $tokenData = null;

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
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->messageId ?? null,
            $this->provider ?? null,
            $this->merchantNumber ?? null,
            $this->tokenData ?? null,
        ]);
    }
}
