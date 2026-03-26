<?php

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\TokenStatusRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\TokenStatusRequest
 */
class TokenStatusRequestTest extends TestCase
{
    public function testSetAndGetTokenData(): void
    {
        $request = new TokenStatusRequest();
        $request->setTokenData('token-abc-123');
        $this->assertSame('token-abc-123', $request->getTokenData());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new TokenStatusRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setTokenData('token-abc-123');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1',
            '0300',
            'merchant-001',
            'token-abc-123',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestWithoutTokenData(): void
    {
        $request = new TokenStatusRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1',
            '0300',
            'merchant-001',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }
}
