<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\CardDetailInterface;

/**
 * Class CardDetail
 */
class CardDetail
    extends Response
    implements CardDetailInterface, DigestInterface
{
    use DigestTrait;

    protected ?string $brandId = null;
    protected ?string $brandName = null;
    protected ?string $cardHolderName = null;
    protected ?string $expiryMonth = null;
    protected ?string $expiryYear = null;
    protected ?string $cardId = null;
    protected ?string $lastFour = null;
    protected ?string $cardAlias = null;

    /**
     * @return string|null
     */
    public function getBrandId(): ?string
    {
        return $this->brandId;
    }

    /**
     * @return string|null
     */
    public function getBrandName(): ?string
    {
        return $this->brandName;
    }

    /**
     * @return string|null
     */
    public function getCardHolderName(): ?string
    {
        return $this->cardHolderName;
    }

    /**
     * @return string|null
     */
    public function getExpiryMonth(): ?string
    {
        return $this->expiryMonth;
    }

    /**
     * @return string|null
     */
    public function getExpiryYear(): ?string
    {
        return $this->expiryYear;
    }

    /**
     * @return string|null
     */
    public function getCardId(): ?string
    {
        return $this->cardId;
    }

    /**
     * @return string|null
     */
    public function getLastFour(): ?string
    {
        return $this->lastFour;
    }

    /**
     * @return string|null
     */
    public function getCardAlias(): ?string
    {
        return $this->cardAlias;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->brandId ?? null,
            $this->brandName ?? null,
            $this->cardHolderName ?? null,
            $this->expiryMonth ?? null,
            $this->expiryYear ?? null,
            $this->cardId ?? null,
            $this->lastFour ?? null,
            $this->cardAlias ?? null,
        ]);
    }
}
