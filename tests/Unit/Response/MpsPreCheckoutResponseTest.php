<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\MpsPreCheckoutResponse;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * @covers \Pronnect\GpWebPay\Response\MpsPreCheckoutResponse
 */
class MpsPreCheckoutResponseTest extends TestCase
{
    private function makeResponse(array $props): MpsPreCheckoutResponse
    {
        $response = new MpsPreCheckoutResponse();
        $reflection = new ReflectionClass($response);
        foreach ($props as $name => $value) {
            $property = $reflection->getProperty($name);
            $property->setAccessible(true);
            $property->setValue($response, $value);
        }
        return $response;
    }

    public function testGetters(): void
    {
        $data = (object)['cards' => []];
        $response = $this->makeResponse([
            'messageId'             => 'msg-1',
            'preCheckoutData'       => $data,
            'walletPartnerLogoUrl'  => 'https://example.com/wallet.png',
            'masterpassLogoUrl'     => 'https://example.com/mp.png',
        ]);

        $this->assertSame('msg-1', $response->getMessageId());
        $this->assertSame($data, $response->getPreCheckoutData());
        $this->assertSame('https://example.com/wallet.png', $response->getWalletPartnerLogoUrl());
        $this->assertSame('https://example.com/mp.png', $response->getMasterpassLogoUrl());
    }

    public function testGetDigestWithAllFields(): void
    {
        $response = $this->makeResponse([
            'messageId'            => 'msg-1',
            'walletPartnerLogoUrl' => 'https://example.com/wallet.png',
            'masterpassLogoUrl'    => 'https://example.com/mp.png',
        ]);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1',
            'https://example.com/wallet.png',
            'https://example.com/mp.png',
        ]);

        $this->assertSame($expected, $response->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $response = $this->makeResponse([
            'messageId'            => 'msg-1',
            'walletPartnerLogoUrl' => 'https://example.com/wallet.png',
        ]);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', 'https://example.com/wallet.png',
        ]);

        $this->assertSame($expected, $response->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new MpsPreCheckoutResponse())->getDigest());
    }
}