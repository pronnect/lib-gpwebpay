<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPayApi\Request\PaymentRequestInterface;

trait PaymentRequestTrait
{
    private ?string $paymentNumber = null;

    /**
     * @return string|null
     */
    public function getPaymentNumber(): ?string
    {
        return $this->paymentNumber;
    }

    /**
     * @param string $paymentNumber
     *
     * @return void
     */
    public function setPaymentNumber(string $paymentNumber): PaymentRequestInterface
    {
        $this->paymentNumber = $paymentNumber;

        return $this;
    }
}
