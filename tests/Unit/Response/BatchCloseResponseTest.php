<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\BatchCloseResponse;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * @covers \Pronnect\GpWebPay\Response\BatchCloseResponse
 */
class BatchCloseResponseTest extends TestCase
{
    private function makeResponse(array $props): BatchCloseResponse
    {
        $response = new BatchCloseResponse();
        $reflection = new ReflectionClass($response);
        foreach ($props as $name => $value) {
            $property = $reflection->getProperty($name);
            $property->setAccessible(true);
            $property->setValue($response, $value);
        }
        return $response;
    }

    public function testGetMessageId(): void
    {
        $response = $this->makeResponse(['messageId' => 'msg-batch-1']);
        $this->assertSame('msg-batch-1', $response->getMessageId());
    }

    public function testGetDigestWithMessageId(): void
    {
        $response = $this->makeResponse(['messageId' => 'msg-batch-1']);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, ['msg-batch-1']);

        $this->assertSame($expected, $response->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new BatchCloseResponse())->getDigest());
    }
}