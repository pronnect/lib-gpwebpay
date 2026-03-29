<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\PaymentRequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class PrepaidPaymentRequest
 *
 * Uses recPaymentType2 group from WSDL (identical to RegularSubscriptionPaymentRequest).
 */
class PrepaidPaymentRequest implements PaymentRequestInterface, SignedInterface
{
    use RequestTrait;
    use PaymentRequestTrait;
    use DigestTrait;
    use SignedTrait;

    private ?string $masterPaymentNumber = null;
    private ?string $orderNumber = null;
    private ?string $referenceNumber = null;
    private ?int $subscriptionAmount = null;
    private ?int $captureFlag = null;
    private ?SubMerchantData $subMerchantData = null;
    private ?CardHolderData $cardHolderData = null;
    private ?PaymentInfo $paymentInfo = null;
    private ?ShoppingCartInfo $shoppingCartInfo = null;
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

    public function getSubscriptionAmount(): ?int
    {
        return $this->subscriptionAmount;
    }

    public function setSubscriptionAmount(int $subscriptionAmount): self
    {
        $this->subscriptionAmount = $subscriptionAmount;

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
            $this->masterPaymentNumber ?? null,
            $this->orderNumber ?? null,
            $this->referenceNumber ?? null,
            $this->subscriptionAmount ?? null,
            $this->captureFlag ?? null,
            $this->subMerchantData?->getDigest(),
            $this->cardHolderData?->getDigest(),
            $this->paymentInfo?->getDigest(),
            $this->shoppingCartInfo?->getDigest(),
            $this->altTerminalData?->getDigest(),
        ]);
    }
}
