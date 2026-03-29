<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request\AddInfo;

/**
 * Digital wallet details for ADDINFO v5.
 * Spec: kap. 10.3, element walletDetails.
 */
class WalletDetails
{
    private ?string $paymentData    = null;
    private ?string $type           = null; // e.g. "GOOGLEPAY", "APPLEPAY"

    public function setPaymentData(string $data): self { $this->paymentData = $data; return $this; }
    public function setType(string $type): self        { $this->type = $type; return $this; }

    public function toXml(): ?string
    {
        $content = '';

        if ($this->paymentData !== null) {
            $content .= '<paymentData>' . htmlspecialchars($this->paymentData, ENT_XML1, 'UTF-8') . '</paymentData>';
        }
        if ($this->type !== null) {
            $content .= '<type>' . htmlspecialchars($this->type, ENT_XML1, 'UTF-8') . '</type>';
        }

        return $content !== '' ? '<walletDetails>' . $content . '</walletDetails>' : null;
    }
}
