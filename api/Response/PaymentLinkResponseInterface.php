<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface PaymentLinkResponseInterface
 *
 * @api
 */
interface PaymentLinkResponseInterface
{
    /**
     * @return string|null
     */
    public function getPaymentLink(): ?string;
}
