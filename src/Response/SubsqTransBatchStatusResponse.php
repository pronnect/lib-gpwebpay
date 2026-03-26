<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\MessageInterface;
use Pronnect\GpWebPayApi\Response\SubsqTransBatchStatusResponseInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class SubsqTransBatchStatusResponse
 */
class SubsqTransBatchStatusResponse
    extends Response
    implements SubsqTransBatchStatusResponseInterface, MessageInterface, DigestInterface, SignedInterface
{
    use MessageTrait;
    use DigestTrait;
    use SignedTrait;

    protected ?string $batchStatus = null;
    protected ?string $errorDescription = null;

    /**
     * @return string|null
     */
    public function getBatchStatus(): ?string
    {
        return $this->batchStatus;
    }

    /**
     * @return string|null
     */
    public function getErrorDescription(): ?string
    {
        return $this->errorDescription;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->getMessageId(),
            $this->batchStatus ?? null,
            $this->errorDescription ?? null,
        ]);
    }
}
