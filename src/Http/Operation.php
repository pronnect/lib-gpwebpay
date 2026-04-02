<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http;

/**
 * GP Webpay HTTP API operation types (OPERATION parameter).
 */
class Operation
{
    /** Standard card payment — initiates payment on the GP Webpay payment page. */
    public const CREATE_ORDER      = 'CREATE_ORDER';

    /**
     * Card verification — verifies card validity without blocking funds.
     * Use for COF (card-on-file) registration. AMOUNT is optional (defaults to 0).
     *
     * ⚠️ Do NOT use a minimum-amount payment + reversal for COF registration.
     * Card associations (Visa/MC) have prohibited this practice since 2021.
     */
    public const CARD_VERIFICATION = 'CARD_VERIFICATION';
}
