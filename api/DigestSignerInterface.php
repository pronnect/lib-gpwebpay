<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi;

/**
 * Interface DigestSignerInterface
 *
 * @api
 */
interface DigestSignerInterface
{
    /**
     * @param string $digest
     *
     * @return string
     */
    public function sign(string $digest): string;

    /**
     * @param string $digest
     * @param string $signature
     *
     * @return bool
     */
    public function verify(string $digest, string $signature): bool;
}
