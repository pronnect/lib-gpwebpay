<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\BatchCloseResponseInterface;
use Pronnect\GpWebPayApi\Response\MessageInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class BatchCloseResponse
 */
class BatchCloseResponse
    extends Response
    implements BatchCloseResponseInterface, MessageInterface, DigestInterface, SignedInterface
{
    use MessageTrait;
    use DigestTrait;
    use SignedTrait;

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->getMessageId(),
        ]);
    }
}
