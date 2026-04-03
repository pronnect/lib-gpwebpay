<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\CardDataResponse;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * @covers \Pronnect\GpWebPay\Response\CardDataResponse
 */
class CardDataResponseTest extends TestCase
{
    private function makeResponse(array $props): CardDataResponse
    {
        $response = new CardDataResponse();
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
        $response = $this->makeResponse([
            'messageId'        => 'msg-20',
            'contentType'      => 'image/png',
            'width'            => 200,
            'height'           => 100,
            'data'             => base64_encode('fake-image'),
            'panMasked'        => '411111******1111',
            'expiryMonth'      => 12,
            'expiryYear'       => 2027,
            'association'      => 'VISA',
            'errorDescription' => null,
        ]);

        $this->assertSame('msg-20', $response->getMessageId());
        $this->assertSame('image/png', $response->getContentType());
        $this->assertSame(200, $response->getWidth());
        $this->assertSame(100, $response->getHeight());
        $this->assertSame(base64_encode('fake-image'), $response->getData());
        $this->assertSame('411111******1111', $response->getPanMasked());
        $this->assertSame(12, $response->getExpiryMonth());
        $this->assertSame(2027, $response->getExpiryYear());
        $this->assertSame('VISA', $response->getAssociation());
        $this->assertNull($response->getErrorDescription());
    }

    public function testGetDigestExcludesData(): void
    {
        $response = $this->makeResponse([
            'messageId'   => 'msg-20',
            'contentType' => 'image/png',
            'width'       => 200,
            'height'      => 100,
            'data'        => base64_encode('fake-image'),
            'panMasked'   => '411111******1111',
            'expiryMonth' => 12,
            'expiryYear'  => 2027,
            'association' => 'VISA',
        ]);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-20',
            'image/png',
            '200',
            '100',
            '411111******1111',
            '12',
            '2027',
            'VISA',
        ]);

        $digest = $response->getDigest();
        $this->assertSame($expected, $digest);
        // Verify 'data' field is NOT in the digest
        $this->assertStringNotContainsString(base64_encode('fake-image'), $digest);
    }

    public function testGetDigestFiltersNulls(): void
    {
        $response = $this->makeResponse([
            'messageId'   => 'msg-21',
            'contentType' => 'image/png',
        ]);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-21',
            'image/png',
        ]);

        $this->assertSame($expected, $response->getDigest());
    }
}
