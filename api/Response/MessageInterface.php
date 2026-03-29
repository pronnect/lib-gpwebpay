<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface MessageInterface
 *
 * @api
 */
interface MessageInterface
{
    /**
     * @return string|null
     */
    public function getMessageId(): ?string;
}
