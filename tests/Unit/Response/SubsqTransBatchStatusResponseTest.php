<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\SubsqTransBatchStatusResponse;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * @covers \Pronnect\GpWebPay\Response\SubsqTransBatchStatusResponse
 */
class SubsqTransBatchStatusResponseTest extends TestCase
{
    private function makeResponse(array $props): SubsqTransBatchStatusResponse
    {
        $response = new SubsqTransBatchStatusResponse();
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
            'messageId'        => 'msg-1',
            'batchStatus'      => 'PROCESSED',
            'errorDescription' => null,
        ]);

        $this->assertSame('msg-1', $response->getMessageId());
        $this->assertSame('PROCESSED', $response->getBatchStatus());
        $this->assertNull($response->getErrorDescription());
    }

    public function testGetDigestWithAllFields(): void
    {
        $response = $this->makeResponse([
            'messageId'        => 'msg-1',
            'batchStatus'      => 'PROCESSED',
            'errorDescription' => 'some error',
        ]);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', 'PROCESSED', 'some error',
        ]);

        $this->assertSame($expected, $response->getDigest());
    }

    public function testGetDigestFiltersNulls(): void
    {
        $response = $this->makeResponse([
            'messageId'   => 'msg-1',
            'batchStatus' => 'PROCESSED',
        ]);

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', 'PROCESSED',
        ]);

        $this->assertSame($expected, $response->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new SubsqTransBatchStatusResponse())->getDigest());
    }
}