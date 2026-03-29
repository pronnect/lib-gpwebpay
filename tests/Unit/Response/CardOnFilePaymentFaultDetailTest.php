<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\CardOnFilePaymentFaultDetail;

/**
 * @covers \Pronnect\GpWebPay\Response\CardOnFilePaymentFaultDetail
 */
class CardOnFilePaymentFaultDetailTest extends TestCase
{
    public function testPublicPropertyAccess(): void
    {
        $detail = new CardOnFilePaymentFaultDetail();
        $detail->messageId = 'msg-30';
        $detail->primaryReturnCode = '46';
        $detail->secondaryReturnCode = '300';
        $detail->authenticationLink = 'https://3dsecure.example.com/auth';
        $detail->signature = 'sig-abc';

        $this->assertSame('msg-30', $detail->messageId);
        $this->assertSame('46', $detail->primaryReturnCode);
        $this->assertSame('300', $detail->secondaryReturnCode);
        $this->assertSame('https://3dsecure.example.com/auth', $detail->authenticationLink);
        $this->assertSame('sig-abc', $detail->signature);
    }

    public function testDefaultsAreNull(): void
    {
        $detail = new CardOnFilePaymentFaultDetail();
        $this->assertNull($detail->messageId);
        $this->assertNull($detail->primaryReturnCode);
        $this->assertNull($detail->secondaryReturnCode);
        $this->assertNull($detail->authenticationLink);
        $this->assertNull($detail->signature);
    }
}
