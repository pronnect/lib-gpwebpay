<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\MpsPreCheckoutRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\MpsPreCheckoutRequest
 */
class MpsPreCheckoutRequestTest extends TestCase
{
    public function testSetAndGetFields(): void
    {
        $request = new MpsPreCheckoutRequest();
        $request->setPairingNumber('PAIR-1')
            ->setRequestCardDetails(true)
            ->setRequestShippingDetails(false)
            ->setRequestRewardPrograms(true);

        $this->assertSame('PAIR-1', $request->getPairingNumber());
        $this->assertTrue($request->getRequestCardDetails());
        $this->assertFalse($request->getRequestShippingDetails());
        $this->assertTrue($request->getRequestRewardPrograms());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new MpsPreCheckoutRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPairingNumber('PAIR-1');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'PAIR-1',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $request = new MpsPreCheckoutRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new MpsPreCheckoutRequest())->getDigest());
    }
}