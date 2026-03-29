<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\CardDetail;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * @covers \Pronnect\GpWebPay\Response\CardDetail
 * @covers \Pronnect\GpWebPay\Response\Response::__set
 */
class CardDetailTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetBrandId(): void
    {
        $cardDetail = new CardDetail();
        $reflection = new ReflectionClass($cardDetail);
        $property = $reflection->getProperty('brandId');
        $property->setAccessible(true);
        $property->setValue($cardDetail, 'brand123');

        $this->assertSame('brand123', $cardDetail->getBrandId());
    }

    /**
     * @return void
     */
    public function testGetBrandName(): void
    {
        $cardDetail = new CardDetail();
        $reflection = new ReflectionClass($cardDetail);
        $property = $reflection->getProperty('brandName');
        $property->setAccessible(true);
        $property->setValue($cardDetail, 'Visa');

        $this->assertSame('Visa', $cardDetail->getBrandName());
    }

    /**
     * @return void
     */
    public function testGetCardHolderName(): void
    {
        $cardDetail = new CardDetail();
        $reflection = new ReflectionClass($cardDetail);
        $property = $reflection->getProperty('cardHolderName');
        $property->setAccessible(true);
        $property->setValue($cardDetail, 'John Doe');

        $this->assertSame('John Doe', $cardDetail->getCardHolderName());
    }

    /**
     * @return void
     */
    public function testGetExpiryMonth(): void
    {
        $cardDetail = new CardDetail();
        $reflection = new ReflectionClass($cardDetail);
        $property = $reflection->getProperty('expiryMonth');
        $property->setAccessible(true);
        $property->setValue($cardDetail, '12');

        $this->assertSame('12', $cardDetail->getExpiryMonth());
    }

    /**
     * @return void
     */
    public function testGetExpiryYear(): void
    {
        $cardDetail = new CardDetail();
        $reflection = new ReflectionClass($cardDetail);
        $property = $reflection->getProperty('expiryYear');
        $property->setAccessible(true);
        $property->setValue($cardDetail, '2025');

        $this->assertSame('2025', $cardDetail->getExpiryYear());
    }

    /**
     * @return void
     */
    public function testGetCardId(): void
    {
        $cardDetail = new CardDetail();
        $reflection = new ReflectionClass($cardDetail);
        $property = $reflection->getProperty('cardId');
        $property->setAccessible(true);
        $property->setValue($cardDetail, 'card123');

        $this->assertSame('card123', $cardDetail->getCardId());
    }

    /**
     * @return void
     */
    public function testGetLastFour(): void
    {
        $cardDetail = new CardDetail();
        $reflection = new ReflectionClass($cardDetail);
        $property = $reflection->getProperty('lastFour');
        $property->setAccessible(true);
        $property->setValue($cardDetail, '1234');

        $this->assertSame('1234', $cardDetail->getLastFour());
    }

    /**
     * @return void
     */
    public function testGetCardAlias(): void
    {
        $cardDetail = new CardDetail();
        $reflection = new ReflectionClass($cardDetail);
        $property = $reflection->getProperty('cardAlias');
        $property->setAccessible(true);
        $property->setValue($cardDetail, 'alias123');

        $this->assertSame('alias123', $cardDetail->getCardAlias());
    }

    /**
     * @return void
     */
    public function testGetDigest(): void
    {
        $cardDetail = new CardDetail();
        $reflection = new ReflectionClass($cardDetail);
        $property = $reflection->getProperty('brandId');
        $property->setAccessible(true);
        $property->setValue($cardDetail, 'brand123');
        $property = $reflection->getProperty('brandName');
        $property->setAccessible(true);
        $property->setValue($cardDetail, 'Visa');
        $property = $reflection->getProperty('cardHolderName');
        $property->setAccessible(true);
        $property->setValue($cardDetail, 'John Doe');
        $property = $reflection->getProperty('expiryMonth');
        $property->setAccessible(true);
        $property->setValue($cardDetail, '12');
        $property = $reflection->getProperty('expiryYear');
        $property->setAccessible(true);
        $property->setValue($cardDetail, '2025');
        $property = $reflection->getProperty('cardId');
        $property->setAccessible(true);
        $property->setValue($cardDetail, 'card123');
        $property = $reflection->getProperty('lastFour');
        $property->setAccessible(true);
        $property->setValue($cardDetail, '1234');
        $property = $reflection->getProperty('cardAlias');
        $property->setAccessible(true);
        $property->setValue($cardDetail, 'alias123');

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            'brand123',
            'Visa',
            'John Doe',
            '12',
            '2025',
            'card123',
            '1234',
            'alias123',
        ]);
        $this->assertSame($expectedDigest, $cardDetail->getDigest());
    }
}
