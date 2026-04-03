<?php

declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface ContactInterface
 *
 * @api
 */
interface ContactInterface extends ResponseInterface
{
    /**
     * @return string|null
     */
    public function getFirstName(): ?string;

    /**
     * @return string|null
     */
    public function getLastName(): ?string;

    /**
     * @return string|null
     */
    public function getCountry(): ?string;

    /**
     * @return string|null
     */
    public function getPhone(): ?string;

    /**
     * @return string|null
     */
    public function getEmail(): ?string;
}
