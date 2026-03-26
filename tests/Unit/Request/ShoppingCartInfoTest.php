<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\ShoppingCartInfo;
use Pronnect\GpWebPay\Request\ShoppingCartItem;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\ShoppingCartInfo
 * @uses \Pronnect\GpWebPay\Request\ShoppingCartItem
 */
class ShoppingCartInfoTest extends TestCase
{
    public function testAmountSettersAndGetters(): void
    {
        $cart = new ShoppingCartInfo();
        $cart->setTaxAmount(200)
            ->setShippingAmount(500)
            ->setHandlingAmount(100)
            ->setCartAmount(5000);

        $this->assertSame(200, $cart->getTaxAmount());
        $this->assertSame(500, $cart->getShippingAmount());
        $this->assertSame(100, $cart->getHandlingAmount());
        $this->assertSame(5000, $cart->getCartAmount());
    }

    public function testAddItemCreatesWrapper(): void
    {
        $cart = new ShoppingCartInfo();
        $this->assertNull($cart->getShoppingCartItems());

        $item1 = (new ShoppingCartItem())->setItemDescription('Widget')->setItemQuantity(1)->setItemUnitPrice(1000);
        $item2 = (new ShoppingCartItem())->setItemDescription('Gadget')->setItemQuantity(2)->setItemUnitPrice(500);

        $cart->addItem($item1)->addItem($item2);

        $items = $cart->getShoppingCartItems();
        $this->assertNotNull($items);
        $this->assertIsArray($items->shoppingCartItem);
        $this->assertCount(2, $items->shoppingCartItem);
        $this->assertSame($item1, $items->shoppingCartItem[0]);
        $this->assertSame($item2, $items->shoppingCartItem[1]);
    }

    public function testSettersReturnSelf(): void
    {
        $cart = new ShoppingCartInfo();
        $this->assertSame($cart, $cart->setTaxAmount(0));
        $this->assertSame($cart, $cart->setShippingAmount(0));
        $this->assertSame($cart, $cart->setHandlingAmount(0));
        $this->assertSame($cart, $cart->setCartAmount(0));
        $this->assertSame($cart, $cart->addItem(new ShoppingCartItem()));
    }

    public function testDefaultsAreNull(): void
    {
        $cart = new ShoppingCartInfo();
        $this->assertNull($cart->getTaxAmount());
        $this->assertNull($cart->getShippingAmount());
        $this->assertNull($cart->getHandlingAmount());
        $this->assertNull($cart->getCartAmount());
        $this->assertNull($cart->getShoppingCartItems());
    }

    public function testGetDigestWithAmounts(): void
    {
        $cart = new ShoppingCartInfo();
        $cart->setTaxAmount(200)->setShippingAmount(500)->setCartAmount(5000);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [200, 500, 5000]);
        $this->assertSame($expected, $cart->getDigest());
    }

    public function testGetDigestIncludesItemDigests(): void
    {
        $item1 = (new ShoppingCartItem())->setItemDescription('Widget')->setItemQuantity(1)->setItemUnitPrice(1000);
        $item2 = (new ShoppingCartItem())->setItemDescription('Gadget')->setItemQuantity(2)->setItemUnitPrice(500);

        $cart = new ShoppingCartInfo();
        $cart->setTaxAmount(100)->addItem($item1)->addItem($item2);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            100,
            $item1->getDigest(),
            $item2->getDigest(),
        ]);

        $this->assertSame($expected, $cart->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new ShoppingCartInfo())->getDigest());
    }
}
