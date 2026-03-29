<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\SubsqTransBatchStatusRequest;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\SubsqTransBatchStatusRequest
 */
class SubsqTransBatchStatusRequestTest extends TestCase
{
    public function testSetAndGetImportFileId(): void
    {
        $request = new SubsqTransBatchStatusRequest();
        $request->setImportFileId('FILE-123');
        $this->assertSame('FILE-123', $request->getImportFileId());
    }

    public function testSetAndGetFileName(): void
    {
        $request = new SubsqTransBatchStatusRequest();
        $request->setFileName('batch.csv');
        $this->assertSame('batch.csv', $request->getFileName());
    }

    public function testGetDigestWithImportFileId(): void
    {
        $request = new SubsqTransBatchStatusRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setImportFileId('FILE-123');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'FILE-123',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestWithFileName(): void
    {
        $request = new SubsqTransBatchStatusRequest();
        $request->setMessageId('msg-1')
            ->setProvider('0300')
            ->setMerchantNumber('merchant-001')
            ->setFileName('batch.csv');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, [
            'msg-1', '0300', 'merchant-001', 'batch.csv',
        ]);

        $this->assertSame($expected, $request->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new SubsqTransBatchStatusRequest())->getDigest());
    }
}