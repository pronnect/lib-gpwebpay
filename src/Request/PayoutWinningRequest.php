<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\PaymentRequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class PayoutWinningRequest
 *
 * Uses payoutType group. registrationKey is an XOR choice: set either
 * masterPaymentNumber or tokenData — not both.
 */
class PayoutWinningRequest implements PaymentRequestInterface, SignedInterface
{
    use RequestTrait;
    use PaymentRequestTrait;
    use DigestTrait;
    use SignedTrait;

    private ?string $masterPaymentNumber = null;
    private ?string $tokenData = null;
    private ?string $orderNumber = null;
    private ?string $referenceNumber = null;
    private ?int $amount = null;
    private ?int $currencyCode = null;

    public function getMasterPaymentNumber(): ?string
    {
        return $this->masterPaymentNumber;
    }

    public function setMasterPaymentNumber(string $masterPaymentNumber): self
    {
        $this->masterPaymentNumber = $masterPaymentNumber;

        return $this;
    }

    public function getTokenData(): ?string
    {
        return $this->tokenData;
    }

    public function setTokenData(string $tokenData): self
    {
        $this->tokenData = $tokenData;

        return $this;
    }

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
            $this->masterPaymentNumber ?? null,
            $this->tokenData ?? null,
            $this->orderNumber ?? null,
            $this->referenceNumber ?? null,
            $this->amount ?? null,
            $this->currencyCode ?? null,
        ]);
    }
}
