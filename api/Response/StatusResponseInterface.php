<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface StatusResponseInterface
 *
 * @api
 */
interface StatusResponseInterface extends ResponseInterface
{
    /**
     * @return string|null
     */
    public function getStatus(): ?string;
}
