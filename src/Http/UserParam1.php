<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http;

/**
 * GP Webpay USERPARAM1 values for card-on-file (COF) and recurring payment registration.
 *
 * Use with CardPaymentRequest::setUserParam1() or CardVerificationRequest::setUserParam1().
 */
class UserParam1
{
    /**
     * Register card for recurring (merchant-initiated) payments.
     * Use with CARD_VERIFICATION operation.
     */
    public const RECURRING_MASTER = 'R';

    /**
     * Register card for card-on-file (COF) — customer-initiated subsequent payments.
     * Use with CARD_VERIFICATION operation.
     */
    public const COF_REGISTRATION = 'T';

    /**
     * COF with 3D Secure — stored card, CVV not required on subsequent payments.
     * Use with CARD_VERIFICATION operation.
     */
    public const COF_3DS_STORED   = 'S';
}
