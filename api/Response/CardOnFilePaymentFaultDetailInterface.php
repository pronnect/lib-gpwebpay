<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface CardOnFilePaymentFaultDetailInterface
 *
 * Fault detail surfaced when processCardOnFilePayment returns PRCODE=46 (soft decline).
 * The cardholder must authenticate via {@see getAuthenticationLink()}.
 *
 * @api
 */
interface CardOnFilePaymentFaultDetailInterface
{
    public function getMessageId(): ?string;

    public function getPrimaryReturnCode(): ?string;

    public function getSecondaryReturnCode(): ?string;

    /** 3-D Secure authentication URL — redirect the cardholder here. */
    public function getAuthenticationLink(): ?string;
}
