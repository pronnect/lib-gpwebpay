<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\RequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class SubsqTransBatchStatusRequest
 *
 * Uses merchantKey (provider + merchantNumber) and subsqKey (importFileId XOR fileName).
 * Set either importFileId or fileName — not both.
 */
class SubsqTransBatchStatusRequest implements RequestInterface, SignedInterface
{
    use RequestTrait;
    use DigestTrait;
    use SignedTrait;

    private ?string $importFileId = null;
    private ?string $fileName = null;

    public function getImportFileId(): ?string
    {
        return $this->importFileId;
    }

    public function setImportFileId(string $importFileId): self
    {
        $this->importFileId = $importFileId;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->messageId ?? null,
            $this->provider ?? null,
            $this->merchantNumber ?? null,
            $this->importFileId ?? null,
            $this->fileName ?? null,
        ]);
    }
}
