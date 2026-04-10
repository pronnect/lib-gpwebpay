<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http;

/**
 * GP Webpay deposit flag values (DEPOSITFLAG parameter).
 */
class DepositFlag
{
    /** Authorization only — funds are blocked but not captured. Capture must be done separately via WS API. */
    public const AUTHORIZE_ONLY    = 0;

    /** Immediate capture — funds are authorized and captured in one step. */
    public const IMMEDIATE_CAPTURE = 1;
}
