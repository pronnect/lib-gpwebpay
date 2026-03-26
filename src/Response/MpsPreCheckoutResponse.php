<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\MessageInterface;
use Pronnect\GpWebPayApi\Response\MpsPreCheckoutResponseInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class MpsPreCheckoutResponse
 */
class MpsPreCheckoutResponse
    extends Response
    implements MpsPreCheckoutResponseInterface, MessageInterface, DigestInterface, SignedInterface
{
    use MessageTrait;
    use DigestTrait;
    use SignedTrait;

    /** @var mixed SOAP-deserialized MpsPreCheckoutData object */
    protected mixed $preCheckoutData = null;
    protected ?string $walletPartnerLogoUrl = null;
    protected ?string $masterpassLogoUrl = null;

    /**
     * @return mixed
     */
    public function getPreCheckoutData(): mixed
    {
        return $this->preCheckoutData;
    }

    /**
     * @return string|null
     */
    public function getWalletPartnerLogoUrl(): ?string
    {
        return $this->walletPartnerLogoUrl;
    }

    /**
     * @return string|null
     */
    public function getMasterpassLogoUrl(): ?string
    {
        return $this->masterpassLogoUrl;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->getMessageId(),
            $this->walletPartnerLogoUrl ?? null,
            $this->masterpassLogoUrl ?? null,
        ]);
    }
}
