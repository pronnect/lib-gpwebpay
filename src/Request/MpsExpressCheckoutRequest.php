<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\PaymentRequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class MpsExpressCheckoutRequest
 */
class MpsExpressCheckoutRequest implements PaymentRequestInterface, SignedInterface
{
    use RequestTrait;
    use PaymentRequestTrait;
    use DigestTrait;
    use SignedTrait;

    private ?string $orderNumber = null;
    private ?string $referenceNumber = null;
    private ?int $amount = null;
    private ?int $currencyCode = null;
    private ?int $captureFlag = null;
    private ?string $pairingNumber = null;
    private ?string $cardId = null;
    private ?string $shippingAddressId = null;

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getReferenceNumber(): ?string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(string $referenceNumber): self
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrencyCode(): ?int
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(int $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function getCaptureFlag(): ?int
    {
        return $this->captureFlag;
    }

    public function setCaptureFlag(int $captureFlag): self
    {
        $this->captureFlag = $captureFlag;

        return $this;
    }

    public function getPairingNumber(): ?string
    {
        return $this->pairingNumber;
    }

    public function setPairingNumber(string $pairingNumber): self
    {
        $this->pairingNumber = $pairingNumber;

        return $this;
    }

    public function getCardId(): ?string
    {
        return $this->cardId;
    }

    public function setCardId(string $cardId): self
    {
        $this->cardId = $cardId;

        return $this;
    }

    public function getShippingAddressId(): ?string
    {
        return $this->shippingAddressId;
    }

    public function setShippingAddressId(string $shippingAddressId): self
    {
        $this->shippingAddressId = $shippingAddressId;

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
            $this->paymentNumber ?? null,
            $this->orderNumber ?? null,
            $this->referenceNumber ?? null,
            $this->amount ?? null,
            $this->currencyCode ?? null,
            $this->captureFlag ?? null,
            $this->pairingNumber ?? null,
            $this->cardId ?? null,
            $this->shippingAddressId ?? null,
        ]);
    }
}
