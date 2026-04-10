<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Request;

/**
 * Interface PaymentRequestInterface
 *
 * @api
 */
interface PaymentRequestInterface extends RequestInterface
{
    /**
     * @param string $paymentNumber
     *
     * @return PaymentRequestInterface
     */
    public function setPaymentNumber(string $paymentNumber): PaymentRequestInterface;

    /**
     * @return string|null
     */
    public function getPaymentNumber(): ?string;
}
