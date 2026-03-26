<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request\AddInfo;

/**
 * Billing address details for ADDINFO v5.
 * Spec: kap. 10.3, element billingDetails.
 */
class BillingDetails
{
    private ?string $city       = null;
    private ?string $country    = null;
    private ?string $address1   = null;
    private ?string $address2   = null;
    private ?string $address3   = null;
    private ?string $zip        = null;
    private ?string $state      = null;

    public function setCity(string $city): self         { $this->city = $city; return $this; }
    public function setCountry(string $country): self   { $this->country = $country; return $this; }
    public function setAddress1(string $addr): self     { $this->address1 = $addr; return $this; }
    public function setAddress2(string $addr): self     { $this->address2 = $addr; return $this; }
    public function setAddress3(string $addr): self     { $this->address3 = $addr; return $this; }
    public function setZip(string $zip): self           { $this->zip = $zip; return $this; }
    public function setState(string $state): self       { $this->state = $state; return $this; }

    public function toXml(): ?string
    {
        $fields = [
            'city'     => $this->city,
            'country'  => $this->country,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'address3' => $this->address3,
            'zip'      => $this->zip,
            'state'    => $this->state,
        ];

        $content = '';
        foreach ($fields as $tag => $value) {
            if ($value !== null) {
                $content .= "<{$tag}>" . htmlspecialchars($value, ENT_XML1, 'UTF-8') . "</{$tag}>";
            }
        }

        return $content !== '' ? '<billingDetails>' . $content . '</billingDetails>' : null;
    }
}
