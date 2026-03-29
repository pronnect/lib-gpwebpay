<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;

/**
 * Class ShippingDetails
 *
 * Shipping address for CardHolderData (3DS2).
 * Required: name, address1, city, postalCode, country.
 * Optional: address2, address3, countrySubdivision, phone, email, method.
 */
class ShippingDetails
{
    use DigestTrait;

    private ?string $name = null;
    private ?string $address1 = null;
    private ?string $address2 = null;
    private ?string $address3 = null;
    private ?string $city = null;
    private ?string $postalCode = null;
    private ?string $country = null;
    private ?string $countrySubdivision = null;
    private ?string $phone = null;
    private ?string $email = null;
    private ?string $method = null;

    public function __get(string $name): mixed
    {
        return $this->$name ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->$name) && $this->$name !== null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(string $address1): self
    {
        $this->address1 = $address1;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getAddress3(): ?string
    {
        return $this->address3;
    }

    public function setAddress3(string $address3): self
    {
        $this->address3 = $address3;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCountrySubdivision(): ?string
    {
        return $this->countrySubdivision;
    }

    public function setCountrySubdivision(string $countrySubdivision): self
    {
        $this->countrySubdivision = $countrySubdivision;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->name,
            $this->address1,
            $this->address2,
            $this->address3,
            $this->city,
            $this->postalCode,
            $this->country,
            $this->countrySubdivision,
            $this->phone,
            $this->email,
            $this->method,
        ]);
    }
}
