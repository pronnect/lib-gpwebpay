<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\CardOnFilePaymentFaultDetail;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Response\CardOnFilePaymentFaultDetail
 * @uses \Pronnect\GpWebPay\ServiceException
 * @uses \Pronnect\GpWebPay\ReturnCodeResolver
 * @uses \Pronnect\GpWebPay\DigestTrait
 * @uses \Pronnect\GpWebPay\SignedTrait
 * @uses \Pronnect\GpWebPay\Response\MessageTrait
 */
class CardOnFilePaymentFaultDetailTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $detail = new CardOnFilePaymentFaultDetail();
        $detail->setMessageId('msg-30');
        $detail->setPrimaryReturnCode('46');
        $detail->setSecondaryReturnCode('300');
        $detail->setAuthenticationLink('https://3dsecure.example.com/auth');
        $detail->setSignature('sig-abc');

        $this->assertSame('msg-30', $detail->getMessageId());
        $this->assertSame('46', $detail->getPrimaryReturnCode());
        $this->assertSame('300', $detail->getSecondaryReturnCode());
        $this->assertSame('https://3dsecure.example.com/auth', $detail->getAuthenticationLink());
        $this->assertSame('sig-abc', $detail->getSignature());
    }

    public function testDefaultsAreNull(): void
    {
        $detail = new CardOnFilePaymentFaultDetail();
        $this->assertNull($detail->getMessageId());
        $this->assertNull($detail->getPrimaryReturnCode());
        $this->assertNull($detail->getSecondaryReturnCode());
        $this->assertNull($detail->getAuthenticationLink());
        $this->assertNull($detail->getSignature());
    }

    public function testGetDigestIncludesAllFields(): void
    {
        $detail = new CardOnFilePaymentFaultDetail();
        $detail->setMessageId('msg-1');
        $detail->setPrimaryReturnCode('46');
        $detail->setSecondaryReturnCode('300');
        $detail->setAuthenticationLink('https://3dsecure.example.com/auth?x=1');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1',
            '46',
            '300',
            'https://3dsecure.example.com/auth?x=1',
        ]);

        $this->assertSame($expected, $detail->getDigest());
    }

    public function testSetAuthenticationLink(): void
    {
        $detail = new CardOnFilePaymentFaultDetail();
        $detail->setAuthenticationLink('https://3dsecure.example.com/auth');

        $this->assertSame('https://3dsecure.example.com/auth', $detail->getAuthenticationLink());
    }

    public function testSetAuthenticationLinkNull(): void
    {
        $detail = new CardOnFilePaymentFaultDetail();
        $detail->setAuthenticationLink('https://3dsecure.example.com/auth');
        $detail->setAuthenticationLink(null);

        $this->assertNull($detail->getAuthenticationLink());
    }

    public function testGetDigestOmitsNullAuthLink(): void
    {
        $detail = new CardOnFilePaymentFaultDetail();
        $detail->setMessageId('msg-2');
        $detail->setPrimaryReturnCode('46');
        $detail->setSecondaryReturnCode('300');
        // authenticationLink left null

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, ['msg-2', '46', '300']);

        $this->assertSame($expected, $detail->getDigest());
    }
}
