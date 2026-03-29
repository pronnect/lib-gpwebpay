<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface PaymentResponseInterface
 *
 * @api
 */
interface PaymentResponseInterface extends ResponseInterface
{
    /**
     * @return string|null
     */
    public function getPaymentNumber(): ?string;
}
