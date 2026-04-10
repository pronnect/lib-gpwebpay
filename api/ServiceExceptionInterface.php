<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi;

use Pronnect\GpWebPayApi\Response\MessageInterface;

/**
 * Interface ServiceExceptionInterface
 *
 * @api
 */
interface ServiceExceptionInterface extends MessageInterface, SignedInterface
{
    /**
     * @return string|null
     */
    public function getPrimaryReturnCode(): ?string;

    /**
     * @return string|null
     */
    public function getSecondaryReturnCode(): ?string;
}
