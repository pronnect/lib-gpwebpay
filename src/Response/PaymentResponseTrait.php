<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

trait PaymentResponseTrait
{
    protected ?string $paymentNumber = null;

    /**
     * @return string|null
     */
    public function getPaymentNumber(): ?string
    {
        return $this->paymentNumber;
    }
}
