<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request\AddInfo;

/**
 * Payment information for ADDINFO v5.
 * Spec: GPwebpayAdditionalInfoRequest_v.5.xsd, element paymentInfo.
 */
class PaymentInfo
{
    private ?string $transactionType    = null;
    private ?string $recurringExpiry    = null; // YYYYMMDD
    private ?string $recurringFrequency = null; // number of days

    public function setTransactionType(string $type): self     { $this->transactionType    = $type;  return $this; }
    public function setRecurringExpiry(string $date): self     { $this->recurringExpiry    = $date;  return $this; }
    public function setRecurringFrequency(string $days): self  { $this->recurringFrequency = $days;  return $this; }

    public function toXml(): ?string
    {
        $fields = [
            'transactionType'    => $this->transactionType,
            'recurringExpiry'    => $this->recurringExpiry,
            'recurringFrequency' => $this->recurringFrequency,
        ];

        $content = '';
        foreach ($fields as $tag => $value) {
            if ($value !== null) {
                $content .= "<{$tag}>" . htmlspecialchars($value, ENT_XML1, 'UTF-8') . "</{$tag}>";
            }
        }

        return $content !== '' ? '<paymentInfo>' . $content . '</paymentInfo>' : null;
    }
}
