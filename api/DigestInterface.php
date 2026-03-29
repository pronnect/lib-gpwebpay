<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi;

/**
 * Interface DigestInterface
 *
 * @api
 */
interface DigestInterface
{
    public const DIGEST_SEPARATOR = '|';

    /**
     * @return string|null
     */
    public function getDigest(): ?string;
}
