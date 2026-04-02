<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request\AddInfo;

/**
 * Payment information for ADDINFO v5.
 * Spec: kap. 10.3, element paymentInfo.
 */
class PaymentInfo
{
    private ?string $transactionType     = null; // 01=goods/service, 02=check acceptance, 03=financing, 04=cash advance, 05=pre-order
    private ?string $recurringExpiry     = null; // YYYYMMDD — last date of recurring
    private ?string $recurringFrequency  = null; // days between transactions
    private ?string $installment         = null; // number of installments

    public function setTransactionType(string $type): self        { $this->transactionType = $type; return $this; }
    public function setRecurringExpiry(string $date): self        { $this->recurringExpiry = $date; return $this; }
    public function setRecurringFrequency(string $days): self     { $this->recurringFrequency = $days; return $this; }
    public function setInstallment(string $installment): self     { $this->installment = $installment; return $this; }

    public function toXml(): ?string
    {
        $fields = [
            'transactionType'    => $this->transactionType,
            'recurringExpiry'    => $this->recurringExpiry,
            'recurringFrequency' => $this->recurringFrequency,
            'installment'        => $this->installment,
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
