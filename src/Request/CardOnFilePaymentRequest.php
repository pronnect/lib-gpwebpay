<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\RequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class CardOnFilePaymentRequest
 */
class CardOnFilePaymentRequest implements RequestInterface, SignedInterface
{
    use RequestTrait;
    use DigestTrait;
    use SignedTrait;

    private ?string $paymentNumber = null;
    private ?int $amount = null;
    private ?int $currencyCode = null;
    private ?int $captureFlag = null;
    private ?SubMerchantData $subMerchantData = null;
    private ?string $tokenData = null;
    private ?CardHolderData $cardHolderData = null;
    private ?PaymentInfo $paymentInfo = null;
    private ?ShoppingCartInfo $shoppingCartInfo = null;
    private ?AltTerminalData $altTerminalData = null;
    private ?string $returnUrl = null;
    private ?string $orderNumber = null;
    private ?string $referenceNumber = null;

    /**
     * @return string|null
     */
    public function getPaymentNumber(): ?string
    {
        return $this->paymentNumber;
    }

    /**
     * @param string $paymentNumber
     *
     * @return $this
     */
    public function setPaymentNumber(string $paymentNumber): self
    {
        $this->paymentNumber = $paymentNumber;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     *
     * @return $this
     */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCurrencyCode(): ?int
    {
        return $this->currencyCode;
    }

    /**
     * @param int $currencyCode
     *
     * @return $this
     */
    public function setCurrencyCode(int $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCaptureFlag(): ?int
    {
        return $this->captureFlag;
    }

    /**
     * @param int $captureFlag
     *
     * @return $this
     */
    public function setCaptureFlag(int $captureFlag): self
    {
        $this->captureFlag = $captureFlag;

        return $this;
    }

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
    public function getReturnUrl(): ?string
    {
        return $this->returnUrl;
    }

    /**
     * @param string $returnUrl
     *
     * @return $this
     */
    public function setReturnUrl(string $returnUrl): self
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    /**
     * @param string $orderNumber
     *
     * @return $this
     */
    public function setOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getReferenceNumber(): ?string
    {
        return $this->referenceNumber;
    }

    /**
     * @param string $referenceNumber
     *
     * @return $this
     */
    public function setReferenceNumber(string $referenceNumber): self
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    public function getSubMerchantData(): ?SubMerchantData
    {
        return $this->subMerchantData;
    }

    public function setSubMerchantData(SubMerchantData $subMerchantData): self
    {
        $this->subMerchantData = $subMerchantData;

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

    public function getPaymentInfo(): ?PaymentInfo
    {
        return $this->paymentInfo;
    }

    public function setPaymentInfo(PaymentInfo $paymentInfo): self
    {
        $this->paymentInfo = $paymentInfo;

        return $this;
    }

    public function getShoppingCartInfo(): ?ShoppingCartInfo
    {
        return $this->shoppingCartInfo;
    }

    public function setShoppingCartInfo(ShoppingCartInfo $shoppingCartInfo): self
    {
        $this->shoppingCartInfo = $shoppingCartInfo;

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
            $this->orderNumber ?? null,
            $this->referenceNumber ?? null,
            $this->amount ?? null,
            $this->currencyCode ?? null,
            $this->captureFlag ?? null,
            $this->subMerchantData?->getDigest(),
            $this->tokenData ?? null,
            $this->cardHolderData?->getDigest(),
            $this->paymentInfo?->getDigest(),
            $this->shoppingCartInfo?->getDigest(),
            $this->altTerminalData?->getDigest(),
            $this->returnUrl ?? null,
        ]);
    }
}
