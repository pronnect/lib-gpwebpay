<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface AddressDetailsInterface
 *
 * @api
 */
interface AddressDetailsInterface extends ResponseInterface
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @return string|null
     */
    public function getAddress1(): ?string;

    /**
     * @return string|null
     */
    public function getAddress2(): ?string;

    /**
     * @return string|null
     */
    public function getAddress3(): ?string;

    /**
     * @return string|null
     */
    public function getCity(): ?string;

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string;

    /**
     * @return string|null
     */
    public function getCountry(): ?string;

    /**
     * @return string|null
     */
    public function getCountrySubdivision(): ?string;

    /**
     * @return string|null
     */
    public function getPhone(): ?string;

    /**
     * @return string|null
     */
    public function getEmail(): ?string;
}
