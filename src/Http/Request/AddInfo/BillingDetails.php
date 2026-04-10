<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request\AddInfo;

/**
 * Billing address details for ADDINFO v5.
 * Spec: kap. 10.3, element billingDetails.
 */
class BillingDetails
{
    private ?string $name                = null;
    private ?string $address1            = null;
    private ?string $address2            = null;
    private ?string $address3            = null;
    private ?string $city                = null;
    private ?string $postalCode          = null;
    private ?string $country             = null;
    private ?string $countrySubdivision  = null;
    private ?string $phone               = null;
    private ?string $email               = null;

    public function setName(string $name): self                        { $this->name = $name; return $this; }
    public function setAddress1(string $addr): self                    { $this->address1 = $addr; return $this; }
    public function setAddress2(string $addr): self                    { $this->address2 = $addr; return $this; }
    public function setAddress3(string $addr): self                    { $this->address3 = $addr; return $this; }
    public function setCity(string $city): self                        { $this->city = $city; return $this; }
    public function setPostalCode(string $zip): self                   { $this->postalCode = $zip; return $this; }
    public function setCountry(string $country): self                  { $this->country = $country; return $this; }
    public function setCountrySubdivision(string $subdivision): self   { $this->countrySubdivision = $subdivision; return $this; }
    public function setPhone(string $phone): self                      { $this->phone = $phone; return $this; }
    public function setEmail(string $email): self                      { $this->email = $email; return $this; }

    public function toXml(): ?string
    {
        $fields = [
            'name'                => $this->name,
            'address1'            => $this->address1,
            'address2'            => $this->address2,
            'address3'            => $this->address3,
            'city'                => $this->city,
            'postalCode'          => $this->postalCode,
            'country'             => $this->country,
            'countrySubdivision'  => $this->countrySubdivision,
            'phone'               => $this->phone,
            'email'               => $this->email,
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
