<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay;

use Pronnect\GpWebPayApi\SignedInterface;

trait SignedTrait
{
    protected ?string $signature;

    /**
     * @return string|null
     */
    public function getSignature(): ?string
    {
        return $this->signature;
    }

    /**
     * @param string $signature
     *
     * @return SignedInterface
     */
    public function setSignature(string $signature): SignedInterface
    {
        $this->signature = $signature;

        return $this;
    }
}
