<?php

declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface LoyaltyProgramDetailsInterface
 *
 * @api
 */
interface LoyaltyProgramDetailsInterface extends ResponseInterface
{
    /**
     * @return string|null
     */
    public function getProgramNumber(): ?string;

    /**
     * @return string|null
     */
    public function getProgramId(): ?string;

    /**
     * @return string|null
     */
    public function getProgramName(): ?string;

    /**
     * @return string|null
     */
    public function getProgramExpiryMonth(): ?string;

    /**
     * @return string|null
     */
    public function getProgramExpiryYear(): ?string;
}
