<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

use Pronnect\GpWebPayApi\ServiceExceptionInterface;

/**
 * Interface CardOnFilePaymentFaultDetailInterface
 *
 * Fault detail surfaced when processCardOnFilePayment returns PRCODE=46 (soft decline).
 * The cardholder must authenticate via {@see getAuthenticationLink()}.
 *
 * @api
 */
interface CardOnFilePaymentFaultDetailInterface extends ServiceExceptionInterface
{
    /**
     * 3-D Secure authentication URL — redirect the cardholder here.
     *
     * @return string|null
     */
    public function getAuthenticationLink(): ?string;
}
