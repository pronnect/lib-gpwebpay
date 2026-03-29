<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;

/**
 * Class CardholderDetails
 *
 * Identity and contact details for the cardholder (3DS2 data).
 * Required: name, email.
 */
class CardholderDetails
{
    use DigestTrait;

    private ?string $name = null;
    private ?string $loginId = null;
    private ?string $loginType = null;
    private ?string $loginTime = null;
    private ?string $userAccountId = null;
    private ?string $userAccountCreatedDate = null;
    private ?string $userAccountAge = null;
    private ?string $userAccountLastChangeDate = null;
    private ?string $userAccountLastChangeAge = null;
    private ?string $userAccountPasswordChangeDate = null;
    private ?string $userAccountPasswordChangeAge = null;
    private ?string $socialNetworkId = null;
    private ?string $email = null;
    private ?string $phoneCountry = null;
    private ?string $phone = null;
    private ?string $mobilePhoneCountry = null;
    private ?string $mobilePhone = null;
    private ?string $workPhoneCountry = null;
    private ?string $workPhone = null;
    private ?string $clientIpAddress = null;

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

    public function getLoginId(): ?string
    {
        return $this->loginId;
    }

    public function setLoginId(string $loginId): self
    {
        $this->loginId = $loginId;

        return $this;
    }

    public function getLoginType(): ?string
    {
        return $this->loginType;
    }

    public function setLoginType(string $loginType): self
    {
        $this->loginType = $loginType;

        return $this;
    }

    public function getLoginTime(): ?string
    {
        return $this->loginTime;
    }

    public function setLoginTime(string $loginTime): self
    {
        $this->loginTime = $loginTime;

        return $this;
    }

    public function getUserAccountId(): ?string
    {
        return $this->userAccountId;
    }

    public function setUserAccountId(string $userAccountId): self
    {
        $this->userAccountId = $userAccountId;

        return $this;
    }

    public function getUserAccountCreatedDate(): ?string
    {
        return $this->userAccountCreatedDate;
    }

    public function setUserAccountCreatedDate(string $userAccountCreatedDate): self
    {
        $this->userAccountCreatedDate = $userAccountCreatedDate;

        return $this;
    }

    public function getUserAccountAge(): ?string
    {
        return $this->userAccountAge;
    }

    public function setUserAccountAge(string $userAccountAge): self
    {
        $this->userAccountAge = $userAccountAge;

        return $this;
    }

    public function getUserAccountLastChangeDate(): ?string
    {
        return $this->userAccountLastChangeDate;
    }

    public function setUserAccountLastChangeDate(string $userAccountLastChangeDate): self
    {
        $this->userAccountLastChangeDate = $userAccountLastChangeDate;

        return $this;
    }

    public function getUserAccountLastChangeAge(): ?string
    {
        return $this->userAccountLastChangeAge;
    }

    public function setUserAccountLastChangeAge(string $userAccountLastChangeAge): self
    {
        $this->userAccountLastChangeAge = $userAccountLastChangeAge;

        return $this;
    }

    public function getUserAccountPasswordChangeDate(): ?string
    {
        return $this->userAccountPasswordChangeDate;
    }

    public function setUserAccountPasswordChangeDate(string $userAccountPasswordChangeDate): self
    {
        $this->userAccountPasswordChangeDate = $userAccountPasswordChangeDate;

        return $this;
    }

    public function getUserAccountPasswordChangeAge(): ?string
    {
        return $this->userAccountPasswordChangeAge;
    }

    public function setUserAccountPasswordChangeAge(string $userAccountPasswordChangeAge): self
    {
        $this->userAccountPasswordChangeAge = $userAccountPasswordChangeAge;

        return $this;
    }

    public function getSocialNetworkId(): ?string
    {
        return $this->socialNetworkId;
    }

    public function setSocialNetworkId(string $socialNetworkId): self
    {
        $this->socialNetworkId = $socialNetworkId;

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

    public function getPhoneCountry(): ?string
    {
        return $this->phoneCountry;
    }

    public function setPhoneCountry(string $phoneCountry): self
    {
        $this->phoneCountry = $phoneCountry;

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

    public function getMobilePhoneCountry(): ?string
    {
        return $this->mobilePhoneCountry;
    }

    public function setMobilePhoneCountry(string $mobilePhoneCountry): self
    {
        $this->mobilePhoneCountry = $mobilePhoneCountry;

        return $this;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    public function setMobilePhone(string $mobilePhone): self
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    public function getWorkPhoneCountry(): ?string
    {
        return $this->workPhoneCountry;
    }

    public function setWorkPhoneCountry(string $workPhoneCountry): self
    {
        $this->workPhoneCountry = $workPhoneCountry;

        return $this;
    }

    public function getWorkPhone(): ?string
    {
        return $this->workPhone;
    }

    public function setWorkPhone(string $workPhone): self
    {
        $this->workPhone = $workPhone;

        return $this;
    }

    public function getClientIpAddress(): ?string
    {
        return $this->clientIpAddress;
    }

    public function setClientIpAddress(string $clientIpAddress): self
    {
        $this->clientIpAddress = $clientIpAddress;

        return $this;
    }

    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->name,
            $this->loginId,
            $this->loginType,
            $this->loginTime,
            $this->userAccountId,
            $this->userAccountCreatedDate,
            $this->userAccountAge,
            $this->userAccountLastChangeDate,
            $this->userAccountLastChangeAge,
            $this->userAccountPasswordChangeDate,
            $this->userAccountPasswordChangeAge,
            $this->socialNetworkId,
            $this->email,
            $this->phoneCountry,
            $this->phone,
            $this->mobilePhoneCountry,
            $this->mobilePhone,
            $this->workPhoneCountry,
            $this->workPhone,
            $this->clientIpAddress,
        ]);
    }
}
