<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request\AddInfo;

use Pronnect\GpWebPay\Http\Exception\HttpRequestException;

/**
 * Shopping cart container for ADDINFO v5.
 * Spec: kap. 10.3, element shoppingCartInfo.
 *
 * Maximum 40 items per spec.
 */
class ShoppingCartInfo
{
    private const MAX_ITEMS = 40;

    /** @var ShoppingCartItem[] */
    private array $items = [];

    /**
     * @throws HttpRequestException if maximum item count (40) would be exceeded
     */
    public function addItem(ShoppingCartItem $item): self
    {
        if (count($this->items) >= self::MAX_ITEMS) {
            throw new HttpRequestException(
                sprintf('ShoppingCartInfo cannot contain more than %d items', self::MAX_ITEMS),
            );
        }
        $this->items[] = $item;
        return $this;
    }

    public function toXml(): ?string
    {
        if (empty($this->items)) {
            return null;
        }

        $itemsXml = '';
        foreach ($this->items as $item) {
            $itemsXml .= $item->toXml();
        }

        return '<shoppingCartInfo><shoppingCartItems>' . $itemsXml . '</shoppingCartItems></shoppingCartInfo>';
    }
}
