<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;

/**
 * Class SubMerchantData
 *
 * Sub-merchant / payment facilitator data for SOAP requests.
 * Required: merchantId, merchantType (MCC), merchantName, merchantStreet,
 *           merchantCity, merchantPostalCode, merchantCountry (ISO 3166-1 Alpha-2),
 *           merchantWeb, merchantServiceNumber.
 * Optional: merchantState, merchantMcAssignedId, merchantCountryOfOrigin.
 */
class SubMerchantData
{
    use DigestTrait;

    private ?string $merchantId = null;
    private ?string $merchantType = null;
    private ?string $merchantName = null;
    private ?string $merchantStreet = null;
    private ?string $merchantCity = null;
    private ?string $merchantPostalCode = null;
    private ?string $merchantState = null;
    private ?string $merchantCountry = null;
    private ?string $merchantWeb = null;
    private ?string $merchantServiceNumber = null;
    private ?string $merchantMcAssignedId = null;
    private ?string $merchantCountryOfOrigin = null;

    public function __get(string $name): mixed
    {
        return $this->$name ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->$name) && $this->$name !== null;
    }

    public function getMerchantId(): ?string
    {
        return $this->merchantId;
    }

    public function setMerchantId(string $merchantId): self
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    /** MCC code — exactly 4 digits. */
    public function getMerchantType(): ?string
    {
        return $this->merchantType;
    }

    public function setMerchantType(string $merchantType): self
    {
        $this->merchantType = $merchantType;

        return $this;
    }

    public function getMerchantName(): ?string
    {
        return $this->merchantName;
    }

    public function setMerchantName(string $merchantName): self
    {
        $this->merchantName = $merchantName;

        return $this;
    }

    public function getMerchantStreet(): ?string
    {
        return $this->merchantStreet;
    }

    public function setMerchantStreet(string $merchantStreet): self
    {
        $this->merchantStreet = $merchantStreet;

        return $this;
    }

    public function getMerchantCity(): ?string
    {
        return $this->merchantCity;
    }

    public function setMerchantCity(string $merchantCity): self
    {
        $this->merchantCity = $merchantCity;

        return $this;
    }

    public function getMerchantPostalCode(): ?string
    {
        return $this->merchantPostalCode;
    }

    public function setMerchantPostalCode(string $merchantPostalCode): self
    {
        $this->merchantPostalCode = $merchantPostalCode;

        return $this;
    }

    /** ISO 3166-1 alpha-3 subdivision code (1-3 chars), optional. */
    public function getMerchantState(): ?string
    {
        return $this->merchantState;
    }

    public function setMerchantState(string $merchantState): self
    {
        $this->merchantState = $merchantState;

        return $this;
    }

    /** ISO 3166-1 Alpha-2 country code — exactly 2 letters. */
    public function getMerchantCountry(): ?string
    {
        return $this->merchantCountry;
    }

    public function setMerchantCountry(string $merchantCountry): self
    {
        $this->merchantCountry = $merchantCountry;

        return $this;
    }

    public function getMerchantWeb(): ?string
    {
        return $this->merchantWeb;
    }

    public function setMerchantWeb(string $merchantWeb): self
    {
        $this->merchantWeb = $merchantWeb;

        return $this;
    }

    /** Customer support phone — digits only, 1-13 chars. */
    public function getMerchantServiceNumber(): ?string
    {
        return $this->merchantServiceNumber;
    }

    public function setMerchantServiceNumber(string $merchantServiceNumber): self
    {
        $this->merchantServiceNumber = $merchantServiceNumber;

        return $this;
    }

    /** MC-assigned sub-merchant ID — alphanumeric, 1-15 chars. */
    public function getMerchantMcAssignedId(): ?string
    {
        return $this->merchantMcAssignedId;
    }

    public function setMerchantMcAssignedId(string $merchantMcAssignedId): self
    {
        $this->merchantMcAssignedId = $merchantMcAssignedId;

        return $this;
    }

    /** 3-digit numeric country-of-origin code. */
    public function getMerchantCountryOfOrigin(): ?string
    {
        return $this->merchantCountryOfOrigin;
    }

    public function setMerchantCountryOfOrigin(string $merchantCountryOfOrigin): self
    {
        $this->merchantCountryOfOrigin = $merchantCountryOfOrigin;

        return $this;
    }

    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->merchantId,
            $this->merchantType,
            $this->merchantName,
            $this->merchantStreet,
            $this->merchantCity,
            $this->merchantPostalCode,
            $this->merchantState,
            $this->merchantCountry,
            $this->merchantWeb,
            $this->merchantServiceNumber,
            $this->merchantMcAssignedId,
            $this->merchantCountryOfOrigin,
        ]);
    }
}
