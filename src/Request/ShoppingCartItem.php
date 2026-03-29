<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;

/**
 * Class ShoppingCartItem
 *
 * Single line-item in a ShoppingCartInfo.
 * Required: itemDescription (max 50 chars), itemQuantity, itemUnitPrice.
 * Optional: itemCode, itemClass, itemType, itemImageUrl (max 2000 chars).
 */
class ShoppingCartItem
{
    use DigestTrait;

    private ?string $itemCode = null;
    private ?string $itemDescription = null;
    private ?int $itemQuantity = null;
    private ?int $itemUnitPrice = null;
    private ?string $itemClass = null;
    private ?string $itemType = null;
    private ?string $itemImageUrl = null;

    public function __get(string $name): mixed
    {
        return $this->$name ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->$name) && $this->$name !== null;
    }

    public function getItemCode(): ?string
    {
        return $this->itemCode;
    }

    public function setItemCode(string $itemCode): self
    {
        $this->itemCode = $itemCode;

        return $this;
    }

    /** Max 50 characters. */
    public function getItemDescription(): ?string
    {
        return $this->itemDescription;
    }

    public function setItemDescription(string $itemDescription): self
    {
        $this->itemDescription = $itemDescription;

        return $this;
    }

    public function getItemQuantity(): ?int
    {
        return $this->itemQuantity;
    }

    public function setItemQuantity(int $itemQuantity): self
    {
        $this->itemQuantity = $itemQuantity;

        return $this;
    }

    public function getItemUnitPrice(): ?int
    {
        return $this->itemUnitPrice;
    }

    public function setItemUnitPrice(int $itemUnitPrice): self
    {
        $this->itemUnitPrice = $itemUnitPrice;

        return $this;
    }

    public function getItemClass(): ?string
    {
        return $this->itemClass;
    }

    public function setItemClass(string $itemClass): self
    {
        $this->itemClass = $itemClass;

        return $this;
    }

    public function getItemType(): ?string
    {
        return $this->itemType;
    }

    public function setItemType(string $itemType): self
    {
        $this->itemType = $itemType;

        return $this;
    }

    /** Max 2000 characters. */
    public function getItemImageUrl(): ?string
    {
        return $this->itemImageUrl;
    }

    public function setItemImageUrl(string $itemImageUrl): self
    {
        $this->itemImageUrl = $itemImageUrl;

        return $this;
    }

    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->itemCode,
            $this->itemDescription,
            $this->itemQuantity,
            $this->itemUnitPrice,
            $this->itemClass,
            $this->itemType,
            $this->itemImageUrl,
        ]);
    }
}
