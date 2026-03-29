<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use stdClass;

/**
 * Class ShoppingCartInfo
 *
 * Shopping cart data for SOAP requests. All fields are optional.
 * Use addItem() to append ShoppingCartItem objects.
 */
class ShoppingCartInfo
{
    use DigestTrait;

    private ?int $taxAmount = null;
    private ?int $shippingAmount = null;
    private ?int $handlingAmount = null;
    private ?int $cartAmount = null;

    /**
     * Wrapper element matching the WSDL structure:
     *   <shoppingCartItems><shoppingCartItem>...</shoppingCartItem></shoppingCartItems>
     *
     * @var stdClass|null
     */
    private ?stdClass $shoppingCartItems = null;

    public function __get(string $name): mixed
    {
        return $this->$name ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->$name) && $this->$name !== null;
    }

    public function getTaxAmount(): ?int
    {
        return $this->taxAmount;
    }

    public function setTaxAmount(int $taxAmount): self
    {
        $this->taxAmount = $taxAmount;

        return $this;
    }

    public function getShippingAmount(): ?int
    {
        return $this->shippingAmount;
    }

    public function setShippingAmount(int $shippingAmount): self
    {
        $this->shippingAmount = $shippingAmount;

        return $this;
    }

    public function getHandlingAmount(): ?int
    {
        return $this->handlingAmount;
    }

    public function setHandlingAmount(int $handlingAmount): self
    {
        $this->handlingAmount = $handlingAmount;

        return $this;
    }

    public function getCartAmount(): ?int
    {
        return $this->cartAmount;
    }

    public function setCartAmount(int $cartAmount): self
    {
        $this->cartAmount = $cartAmount;

        return $this;
    }

    public function getShoppingCartItems(): ?stdClass
    {
        return $this->shoppingCartItems;
    }

    public function addItem(ShoppingCartItem $item): self
    {
        if ($this->shoppingCartItems === null) {
            $this->shoppingCartItems = new stdClass();
            $this->shoppingCartItems->shoppingCartItem = [];
        }
        $this->shoppingCartItems->shoppingCartItem[] = $item;

        return $this;
    }

    public function getDigest(): ?string
    {
        $itemDigests = [];
        if ($this->shoppingCartItems !== null) {
            foreach ($this->shoppingCartItems->shoppingCartItem as $item) {
                $digest = $item->getDigest();
                if ($digest !== null) {
                    $itemDigests[] = $digest;
                }
            }
        }

        return $this->makeDigest([
            $this->taxAmount,
            $this->shippingAmount,
            $this->handlingAmount,
            $this->cartAmount,
            ...$itemDigests,
        ]);
    }
}
