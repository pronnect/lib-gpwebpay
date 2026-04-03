<?php

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\TokenRevokeRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\TokenRevokeRequest
 */
class TokenRevokeRequestTest extends TestCase
{
    public function testSetAndGetTokenData(): void
    {
        $request = new TokenRevokeRequest();
        $request->setTokenData('token-xyz-456');
        $this->assertSame('token-xyz-456', $request->getTokenData());
    }

    public function testGetDigestWithAllFields(): void
    {
        $request = new TokenRevokeRequest();
        $request->setMessageId('msg-2')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setTokenData('token-xyz-456');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-2',
            '0300',
            'merchant-001',
            'token-xyz-456',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestWithoutTokenData(): void
    {
        $request = new TokenRevokeRequest();
        $request->setMessageId('msg-2')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-2',
            '0300',
            'merchant-001',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }
}
