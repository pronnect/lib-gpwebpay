<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request\AddInfo;

/**
 * Cardholder personal details for ADDINFO v5.
 * Spec: GPwebpayAdditionalInfoRequest_v.5.xsd, element cardholderDetails.
 *
 * Phone country codes must be digits only (1–3 digits, no '+' prefix).
 * Phone numbers must be digits only (1–15 digits).
 * Country codes must be ISO 3166-1 numeric three-digit strings (e.g. "203" for CZ).
 */
class CardholderDetails
{
    private ?string $name               = null;
    private ?string $email              = null;
    private ?string $phoneCountry       = null;
    private ?string $phone              = null;
    private ?string $mobilePhoneCountry = null;
    private ?string $mobilePhone        = null;
    private ?string $workPhoneCountry   = null;
    private ?string $workPhone          = null;

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Home/main phone — phoneCountry must be 1–3 digits (e.g. "420"), number 1–15 digits.
     */
    public function setPhone(string $countryCode, string $number): self
    {
        $this->phoneCountry = $countryCode;
        $this->phone        = $number;
        return $this;
    }

    /**
     * Mobile phone — mobilePhoneCountry must be 1–3 digits, number 1–15 digits.
     */
    public function setMobilePhone(string $countryCode, string $number): self
    {
        $this->mobilePhoneCountry = $countryCode;
        $this->mobilePhone        = $number;
        return $this;
    }

    /**
     * Work phone — workPhoneCountry must be 1–3 digits, number 1–15 digits.
     */
    public function setWorkPhone(string $countryCode, string $number): self
    {
        $this->workPhoneCountry = $countryCode;
        $this->workPhone        = $number;
        return $this;
    }

    /**
     * Renders the <cardholderDetails> XML fragment.
     * Element order matches XSD xs:sequence exactly.
     * Returns null if no fields are set.
     */
    public function toXml(): ?string
    {
        $content = '';

        if ($this->name !== null) {
            $content .= '<name>' . htmlspecialchars($this->name, ENT_XML1, 'UTF-8') . '</name>';
        }
        if ($this->email !== null) {
            $content .= '<email>' . htmlspecialchars($this->email, ENT_XML1, 'UTF-8') . '</email>';
        }
        if ($this->phoneCountry !== null && $this->phone !== null) {
            $content .= '<phoneCountry>' . htmlspecialchars($this->phoneCountry, ENT_XML1, 'UTF-8') . '</phoneCountry>';
            $content .= '<phone>' . htmlspecialchars($this->phone, ENT_XML1, 'UTF-8') . '</phone>';
        }
        if ($this->mobilePhoneCountry !== null && $this->mobilePhone !== null) {
            $content .= '<mobilePhoneCountry>' . htmlspecialchars($this->mobilePhoneCountry, ENT_XML1, 'UTF-8') . '</mobilePhoneCountry>';
            $content .= '<mobilePhone>' . htmlspecialchars($this->mobilePhone, ENT_XML1, 'UTF-8') . '</mobilePhone>';
        }
        if ($this->workPhoneCountry !== null && $this->workPhone !== null) {
            $content .= '<workPhoneCountry>' . htmlspecialchars($this->workPhoneCountry, ENT_XML1, 'UTF-8') . '</workPhoneCountry>';
            $content .= '<workPhone>' . htmlspecialchars($this->workPhone, ENT_XML1, 'UTF-8') . '</workPhone>';
        }

        if ($content === '') {
            return null;
        }

        return '<cardholderDetails>' . $content . '</cardholderDetails>';
    }
}
