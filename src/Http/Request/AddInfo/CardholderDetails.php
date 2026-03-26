<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request\AddInfo;

/**
 * Cardholder personal details for ADDINFO v5.
 * Spec: kap. 10.3, element cardholderDetails.
 */
class CardholderDetails
{
    private ?string $name       = null;
    private ?string $email      = null;
    private ?string $phoneCountry = null;
    private ?string $phone      = null;
    private ?string $homePhoneCountry = null;
    private ?string $homePhone  = null;
    private ?string $workPhoneCountry = null;
    private ?string $workPhone  = null;

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
     * Mobile phone — both country code and number must be set together or neither.
     */
    public function setPhone(string $countryCode, string $number): self
    {
        $this->phoneCountry = $countryCode;
        $this->phone        = $number;
        return $this;
    }

    /**
     * Home phone — both country code and number must be set together or neither.
     */
    public function setHomePhone(string $countryCode, string $number): self
    {
        $this->homePhoneCountry = $countryCode;
        $this->homePhone        = $number;
        return $this;
    }

    /**
     * Work phone — both country code and number must be set together or neither.
     */
    public function setWorkPhone(string $countryCode, string $number): self
    {
        $this->workPhoneCountry = $countryCode;
        $this->workPhone        = $number;
        return $this;
    }

    /**
     * Renders the <cardholderDetails> XML fragment (no whitespace between tags).
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
            $content .= '<mobilePhone>';
            $content .= '<phoneCountry>' . htmlspecialchars($this->phoneCountry, ENT_XML1, 'UTF-8') . '</phoneCountry>';
            $content .= '<phone>' . htmlspecialchars($this->phone, ENT_XML1, 'UTF-8') . '</phone>';
            $content .= '</mobilePhone>';
        }
        if ($this->homePhoneCountry !== null && $this->homePhone !== null) {
            $content .= '<homePhone>';
            $content .= '<phoneCountry>' . htmlspecialchars($this->homePhoneCountry, ENT_XML1, 'UTF-8') . '</phoneCountry>';
            $content .= '<phone>' . htmlspecialchars($this->homePhone, ENT_XML1, 'UTF-8') . '</phone>';
            $content .= '</homePhone>';
        }
        if ($this->workPhoneCountry !== null && $this->workPhone !== null) {
            $content .= '<workPhone>';
            $content .= '<phoneCountry>' . htmlspecialchars($this->workPhoneCountry, ENT_XML1, 'UTF-8') . '</phoneCountry>';
            $content .= '<phone>' . htmlspecialchars($this->workPhone, ENT_XML1, 'UTF-8') . '</phone>';
            $content .= '</workPhone>';
        }

        if ($content === '') {
            return null;
        }

        return '<cardholderDetails>' . $content . '</cardholderDetails>';
    }
}
