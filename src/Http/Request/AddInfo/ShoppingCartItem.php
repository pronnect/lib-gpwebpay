<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request\AddInfo;

/**
 * Single shopping cart item for ADDINFO v5.
 * Spec: kap. 10.3, element shoppingCartItem.
 */
class ShoppingCartItem
{
    private ?string $description = null;
    private ?int    $quantity    = null;
    private ?int    $price       = null; // in smallest currency units

    public function __construct(
        string $description,
        int $quantity,
        int $price,
    ) {
        $this->description = $description;
        $this->quantity    = $quantity;
        $this->price       = $price;
    }

    public function toXml(): string
    {
        $content = '';

        if ($this->description !== null) {
            $content .= '<itemDescription>' . htmlspecialchars($this->description, ENT_XML1, 'UTF-8') . '</itemDescription>';
        }
        if ($this->quantity !== null) {
            $content .= '<itemQuantity>' . $this->quantity . '</itemQuantity>';
        }
        if ($this->price !== null) {
            $content .= '<itemPrice>' . $this->price . '</itemPrice>';
        }

        return '<shoppingCartItem>' . $content . '</shoppingCartItem>';
    }
}
