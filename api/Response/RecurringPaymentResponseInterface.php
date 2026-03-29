<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface RecurringPaymentResponseInterface
 *
 * Shared by recurring, usage-based subscription, regular subscription, prepaid,
 * payout variants, and Masterpass express-checkout responses.
 *
 * @api
 */
interface RecurringPaymentResponseInterface extends ResponseInterface
{
    public function getAuthCode(): ?string;

    public function getTraceId(): ?string;

    public function getAuthResponseCode(): ?string;

    public function getAuthRRN(): ?string;

    public function getPaymentAccountReference(): ?string;
}
