<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\AddressDetailsInterface;

/**
 * Class AddressDetails
 */
class AddressDetails
    extends Response
    implements AddressDetailsInterface, DigestInterface
{
    use DigestTrait;

    protected ?string $name = null;
    protected ?string $address1 = null;
    protected ?string $address2 = null;
    protected ?string $address3 = null;
    protected ?string $city = null;
    protected ?string $postalCode = null;
    protected ?string $country = null;
    protected ?string $countrySubdivision = null;
    protected ?string $phone = null;
    protected ?string $email = null;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    /**
     * @return string|null
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * @return string|null
     */
    public function getAddress3(): ?string
    {
        return $this->address3;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @return string|null
     */
    public function getCountrySubdivision(): ?string
    {
        return $this->countrySubdivision;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->name ?? null,
            $this->address1 ?? null,
            $this->address2 ?? null,
            $this->address3 ?? null,
            $this->city ?? null,
            $this->postalCode ?? null,
            $this->country ?? null,
            $this->countrySubdivision ?? null,
            $this->phone ?? null,
            $this->email ?? null,
        ]);
    }
}
