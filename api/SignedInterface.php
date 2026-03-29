<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi;

/**
 * Interface SignedInterface
 *
 * @api
 */
interface SignedInterface extends DigestInterface
{
    /**
     * @param string $signature
     *
     * @return SignedInterface
     */
    public function setSignature(string $signature): SignedInterface;

    /**
     * @return string|null
     */
    public function getSignature(): ?string;
}
