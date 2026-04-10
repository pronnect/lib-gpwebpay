<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\ResolvePaymentStatusRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\ResolvePaymentStatusRequest
 */
class ResolvePaymentStatusRequestTest extends TestCase
{
    public function testSetAndGetPaymentStatus(): void
    {
        $request = new ResolvePaymentStatusRequest();
        $request->setPaymentStatus('CR');
        $this->assertSame('CR', $request->getPaymentStatus());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new ResolvePaymentStatusRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-1')
            ->setPaymentStatus('CR');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'PAY-1', 'CR',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $request = new ResolvePaymentStatusRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setPaymentNumber('PAY-1');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'PAY-1',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new ResolvePaymentStatusRequest())->getDigest());
    }
}