<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\MessageInterface;
use Pronnect\GpWebPayApi\Response\StatusResponseInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class StatusResponse
 */
class StatusResponse
    extends Response
    implements StatusResponseInterface, MessageInterface, DigestInterface, SignedInterface
{
    use MessageTrait;
    use DigestTrait;
    use SignedTrait;

    protected ?string $status = null;

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest(
            [
                $this->getMessageId(),
                $this->getStatus(),
            ]
        );
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }
}
