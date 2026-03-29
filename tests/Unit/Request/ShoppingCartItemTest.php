<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\ShoppingCartItem;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\ShoppingCartItem
 */
class ShoppingCartItemTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $item = new ShoppingCartItem();
        $item->setItemCode('SKU-001')
            ->setItemDescription('Widget')
            ->setItemQuantity(3)
            ->setItemUnitPrice(1000)
            ->setItemClass('PHYSICAL')
            ->setItemType('GOODS')
            ->setItemImageUrl('https://example.com/img.png');

        $this->assertSame('SKU-001', $item->getItemCode());
        $this->assertSame('Widget', $item->getItemDescription());
        $this->assertSame(3, $item->getItemQuantity());
        $this->assertSame(1000, $item->getItemUnitPrice());
        $this->assertSame('PHYSICAL', $item->getItemClass());
        $this->assertSame('GOODS', $item->getItemType());
        $this->assertSame('https://example.com/img.png', $item->getItemImageUrl());
    }

    public function testSettersReturnSelf(): void
    {
        $item = new ShoppingCartItem();
        $this->assertSame($item, $item->setItemDescription('Desc'));
        $this->assertSame($item, $item->setItemQuantity(1));
        $this->assertSame($item, $item->setItemUnitPrice(100));
    }

    public function testDefaultsAreNull(): void
    {
        $item = new ShoppingCartItem();
        $this->assertNull($item->getItemCode());
        $this->assertNull($item->getItemDescription());
        $this->assertNull($item->getItemQuantity());
        $this->assertNull($item->getItemUnitPrice());
        $this->assertNull($item->getItemClass());
        $this->assertNull($item->getItemType());
        $this->assertNull($item->getItemImageUrl());
    }

    public function testGetDigestWithRequiredFields(): void
    {
        $item = new ShoppingCartItem();
        $item->setItemDescription('Widget')
            ->setItemQuantity(2)
            ->setItemUnitPrice(500);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, ['Widget', 2, 500]);
        $this->assertSame($expected, $item->getDigest());
    }

    public function testGetDigestWithAllFields(): void
    {
        $item = new ShoppingCartItem();
        $item->setItemCode('SKU-001')
            ->setItemDescription('Widget')
            ->setItemQuantity(2)
            ->setItemUnitPrice(500)
            ->setItemClass('PHY')
            ->setItemType('GOODS')
            ->setItemImageUrl('https://example.com/img.png');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'SKU-001', 'Widget', 2, 500, 'PHY', 'GOODS', 'https://example.com/img.png',
        ]);

        $this->assertSame($expected, $item->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new ShoppingCartItem())->getDigest());
    }
}
