<?php declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Response\StateResponseInterface;

/**
 * Class StateResponse
 */
class StateResponse
    extends StatusResponse
    implements StateResponseInterface
{
    use MessageTrait;
    use DigestTrait;
    use SignedTrait;

    protected ?string $state = null;
    protected ?string $subStatus = null;

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest(
            [
                $this->getMessageId(),
                $this->getState(),
                $this->getStatus(),
                $this->getSubStatus(),
            ]
        );
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @return string|null
     */
    public function getSubStatus(): ?string
    {
        return $this->subStatus;
    }
}
