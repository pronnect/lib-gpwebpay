<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\ContactInterface;

/**
 * Class Contact
 */
class Contact
    extends Response
    implements ContactInterface, DigestInterface
{
    use DigestTrait;

    protected ?string $firstName = null;
    protected ?string $lastName = null;
    protected ?string $country = null;
    protected ?string $phone = null;
    protected ?string $email = null;

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
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
            $this->firstName ?? null,
            $this->lastName ?? null,
            $this->country ?? null,
            $this->phone ?? null,
            $this->email ?? null,
        ]);
    }
}
