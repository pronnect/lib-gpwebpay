<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface StateResponseInterface
 *
 * @api
 */
interface StateResponseInterface extends StatusResponseInterface
{
    /**
     * @return string|null
     */
    public function getState(): ?string;

    /**
     * @return string|null
     */
    public function getSubStatus(): ?string;
}
