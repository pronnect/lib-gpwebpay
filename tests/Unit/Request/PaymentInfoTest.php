<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\PaymentInfo;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\PaymentInfo
 */
class PaymentInfoTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $info = new PaymentInfo();
        $info->setTransactionType('01')
            ->setShippingIndicator('01')
            ->setPreOrderPurchaseInd('01')
            ->setPreOrderDate('20260301')
            ->setReorderItemsInd('01')
            ->setDeliveryTimeframe('01')
            ->setDeliveryEmailAddress('ship@example.com')
            ->setGiftCardCount('2')
            ->setGiftCardAmount('5000')
            ->setGiftCardCurrency('978')
            ->setRecurringExpiry('20270101')
            ->setRecurringFrequency('30')
            ->setRemmitanceInfo1('info1')
            ->setRemmitanceInfo2('info2');

        $this->assertSame('01', $info->getTransactionType());
        $this->assertSame('01', $info->getShippingIndicator());
        $this->assertSame('01', $info->getPreOrderPurchaseInd());
        $this->assertSame('20260301', $info->getPreOrderDate());
        $this->assertSame('01', $info->getReorderItemsInd());
        $this->assertSame('01', $info->getDeliveryTimeframe());
        $this->assertSame('ship@example.com', $info->getDeliveryEmailAddress());
        $this->assertSame('2', $info->getGiftCardCount());
        $this->assertSame('5000', $info->getGiftCardAmount());
        $this->assertSame('978', $info->getGiftCardCurrency());
        $this->assertSame('20270101', $info->getRecurringExpiry());
        $this->assertSame('30', $info->getRecurringFrequency());
        $this->assertSame('info1', $info->getRemmitanceInfo1());
        $this->assertSame('info2', $info->getRemmitanceInfo2());
    }

    public function testSettersReturnSelf(): void
    {
        $info = new PaymentInfo();
        $this->assertSame($info, $info->setTransactionType('01'));
        $this->assertSame($info, $info->setRemmitanceInfo1('x'));
    }

    public function testDefaultsAreNull(): void
    {
        $info = new PaymentInfo();
        $this->assertNull($info->getTransactionType());
        $this->assertNull($info->getDeliveryEmailAddress());
        $this->assertNull($info->getRemmitanceInfo2());
    }

    public function testGetDigestWithSomeFields(): void
    {
        $info = new PaymentInfo();
        $info->setTransactionType('01')
            ->setDeliveryEmailAddress('ship@example.com')
            ->setRecurringExpiry('20270101');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            '01', 'ship@example.com', '20270101',
        ]);

        $this->assertSame($expected, $info->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new PaymentInfo())->getDigest());
    }
}
