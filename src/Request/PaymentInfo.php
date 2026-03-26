<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;

/**
 * Class PaymentInfo
 *
 * 3DS2 / order metadata for SOAP requests. All fields are optional.
 */
class PaymentInfo
{
    use DigestTrait;

    private ?string $transactionType = null;
    private ?string $shippingIndicator = null;
    private ?string $preOrderPurchaseInd = null;
    private ?string $preOrderDate = null;
    private ?string $reorderItemsInd = null;
    private ?string $deliveryTimeframe = null;
    private ?string $deliveryEmailAddress = null;
    private ?string $giftCardCount = null;
    private ?string $giftCardAmount = null;
    private ?string $giftCardCurrency = null;
    private ?string $recurringExpiry = null;
    private ?string $recurringFrequency = null;
    private ?string $remmitanceInfo1 = null;
    private ?string $remmitanceInfo2 = null;

    public function __get(string $name): mixed
    {
        return $this->$name ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->$name) && $this->$name !== null;
    }

    public function getTransactionType(): ?string
    {
        return $this->transactionType;
    }

    public function setTransactionType(string $transactionType): self
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    public function getShippingIndicator(): ?string
    {
        return $this->shippingIndicator;
    }

    public function setShippingIndicator(string $shippingIndicator): self
    {
        $this->shippingIndicator = $shippingIndicator;

        return $this;
    }

    public function getPreOrderPurchaseInd(): ?string
    {
        return $this->preOrderPurchaseInd;
    }

    public function setPreOrderPurchaseInd(string $preOrderPurchaseInd): self
    {
        $this->preOrderPurchaseInd = $preOrderPurchaseInd;

        return $this;
    }

    public function getPreOrderDate(): ?string
    {
        return $this->preOrderDate;
    }

    public function setPreOrderDate(string $preOrderDate): self
    {
        $this->preOrderDate = $preOrderDate;

        return $this;
    }

    public function getReorderItemsInd(): ?string
    {
        return $this->reorderItemsInd;
    }

    public function setReorderItemsInd(string $reorderItemsInd): self
    {
        $this->reorderItemsInd = $reorderItemsInd;

        return $this;
    }

    public function getDeliveryTimeframe(): ?string
    {
        return $this->deliveryTimeframe;
    }

    public function setDeliveryTimeframe(string $deliveryTimeframe): self
    {
        $this->deliveryTimeframe = $deliveryTimeframe;

        return $this;
    }

    public function getDeliveryEmailAddress(): ?string
    {
        return $this->deliveryEmailAddress;
    }

    public function setDeliveryEmailAddress(string $deliveryEmailAddress): self
    {
        $this->deliveryEmailAddress = $deliveryEmailAddress;

        return $this;
    }

    public function getGiftCardCount(): ?string
    {
        return $this->giftCardCount;
    }

    public function setGiftCardCount(string $giftCardCount): self
    {
        $this->giftCardCount = $giftCardCount;

        return $this;
    }

    public function getGiftCardAmount(): ?string
    {
        return $this->giftCardAmount;
    }

    public function setGiftCardAmount(string $giftCardAmount): self
    {
        $this->giftCardAmount = $giftCardAmount;

        return $this;
    }

    public function getGiftCardCurrency(): ?string
    {
        return $this->giftCardCurrency;
    }

    public function setGiftCardCurrency(string $giftCardCurrency): self
    {
        $this->giftCardCurrency = $giftCardCurrency;

        return $this;
    }

    public function getRecurringExpiry(): ?string
    {
        return $this->recurringExpiry;
    }

    public function setRecurringExpiry(string $recurringExpiry): self
    {
        $this->recurringExpiry = $recurringExpiry;

        return $this;
    }

    public function getRecurringFrequency(): ?string
    {
        return $this->recurringFrequency;
    }

    public function setRecurringFrequency(string $recurringFrequency): self
    {
        $this->recurringFrequency = $recurringFrequency;

        return $this;
    }

    public function getRemmitanceInfo1(): ?string
    {
        return $this->remmitanceInfo1;
    }

    public function setRemmitanceInfo1(string $remmitanceInfo1): self
    {
        $this->remmitanceInfo1 = $remmitanceInfo1;

        return $this;
    }

    public function getRemmitanceInfo2(): ?string
    {
        return $this->remmitanceInfo2;
    }

    public function setRemmitanceInfo2(string $remmitanceInfo2): self
    {
        $this->remmitanceInfo2 = $remmitanceInfo2;

        return $this;
    }

    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->transactionType,
            $this->shippingIndicator,
            $this->preOrderPurchaseInd,
            $this->preOrderDate,
            $this->reorderItemsInd,
            $this->deliveryTimeframe,
            $this->deliveryEmailAddress,
            $this->giftCardCount,
            $this->giftCardAmount,
            $this->giftCardCurrency,
            $this->recurringExpiry,
            $this->recurringFrequency,
            $this->remmitanceInfo1,
            $this->remmitanceInfo2,
        ]);
    }
}
