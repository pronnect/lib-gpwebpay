<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\CardDataResponseInterface;
use Pronnect\GpWebPayApi\Response\MessageInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class CardDataResponse
 */
class CardDataResponse
    extends Response
    implements CardDataResponseInterface, MessageInterface, DigestInterface, SignedInterface
{
    use MessageTrait;
    use DigestTrait;
    use SignedTrait;

    protected ?string $contentType = null;
    protected ?int $width = null;
    protected ?int $height = null;
    /** @var string|null Base64Binary image data — NOT included in digest per spec */
    protected ?string $data = null;
    protected ?string $panMasked = null;
    protected ?int $expiryMonth = null;
    protected ?int $expiryYear = null;
    protected ?string $association = null;
    protected ?string $errorDescription = null;

    /**
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @return string|null
     */
    public function getPanMasked(): ?string
    {
        return $this->panMasked;
    }

    /**
     * @return int|null
     */
    public function getExpiryMonth(): ?int
    {
        return $this->expiryMonth;
    }

    /**
     * @return int|null
     */
    public function getExpiryYear(): ?int
    {
        return $this->expiryYear;
    }

    /**
     * @return string|null
     */
    public function getAssociation(): ?string
    {
        return $this->association;
    }

    /**
     * @return string|null
     */
    public function getErrorDescription(): ?string
    {
        return $this->errorDescription;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->getMessageId(),
            $this->contentType ?? null,
            $this->width ?? null,
            $this->height ?? null,
            $this->panMasked ?? null,
            $this->expiryMonth ?? null,
            $this->expiryYear ?? null,
            $this->association ?? null,
            $this->errorDescription ?? null,
        ]);
    }
}
