<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay;

use Pronnect\GpWebPayApi\DigestInterface;

trait DigestTrait
{

    /**
     * @param array $digest
     *
     * @return string|null
     */
    protected function makeDigest(array $digest): ?string
    {
        $digestResult = array_filter($digest, static fn($item) => $item !== null);

        return !empty($digestResult)
            ? implode(DigestInterface::DIGEST_SEPARATOR, $digestResult)
            : null;
    }
}
