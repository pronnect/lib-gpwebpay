<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\PaymentRequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class RecurringPaymentRequest
 */
class RecurringPaymentRequest implements PaymentRequestInterface, SignedInterface
{
    use RequestTrait;
    use PaymentRequestTrait;
    use DigestTrait;
    use SignedTrait;

    private ?string $masterPaymentNumber = null;
    private ?string $orderNumber = null;
    private ?string $referenceNumber = null;
    private ?int $amount = null;
    private ?int $currencyCode = null;
    private ?int $captureFlag = null;
    private ?CardHolderData $cardHolderData = null;
    private ?AltTerminalData $altTerminalData = null;

    public function getMasterPaymentNumber(): ?string
    {
        return $this->masterPaymentNumber;
    }

    public function setMasterPaymentNumber(string $masterPaymentNumber): self
    {
        $this->masterPaymentNumber = $masterPaymentNumber;

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

    public function getCaptureFlag(): ?int
    {
        return $this->captureFlag;
    }

    public function setCaptureFlag(int $captureFlag): self
    {
        $this->captureFlag = $captureFlag;

        return $this;
    }

    public function getCardHolderData(): ?CardHolderData
    {
        return $this->cardHolderData;
    }

    public function setCardHolderData(CardHolderData $cardHolderData): self
    {
        $this->cardHolderData = $cardHolderData;

        return $this;
    }

    public function getAltTerminalData(): ?AltTerminalData
    {
        return $this->altTerminalData;
    }

    public function setAltTerminalData(AltTerminalData $altTerminalData): self
    {
        $this->altTerminalData = $altTerminalData;

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
            $this->orderNumber ?? null,
            $this->referenceNumber ?? null,
            $this->amount ?? null,
            $this->currencyCode ?? null,
            $this->captureFlag ?? null,
            $this->cardHolderData?->getDigest(),
            $this->altTerminalData?->getDigest(),
        ]);
    }
}
