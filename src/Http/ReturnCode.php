<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http;

/**
 * GP Webpay HTTP API return codes (PRCODE parameter in callback).
 *
 * Source: GP Webpay HTTP API spec, kap. 10.2.
 *
 * Use HttpResponseInterface::getPrCode() and HttpResponseInterface::isSuccess()
 * to check the payment result.
 */
class ReturnCode
{
    // ── Success ──────────────────────────────────────────────────────────────
    public const OK                      = 0;

    // ── Input validation errors ──────────────────────────────────────────────
    public const FIELD_TOO_LONG          = 1;
    public const FIELD_TOO_SHORT         = 2;
    public const INCORRECT_CONTENT       = 3;
    public const FIELD_IS_NULL           = 4;
    public const MISSING_REQUIRED        = 5;
    public const MISSING_FIELD           = 6;

    // ── Business errors ──────────────────────────────────────────────────────
    public const UNKNOWN_MERCHANT        = 11;
    public const DUPLICATE_ORDER         = 14;
    public const OBJECT_NOT_FOUND        = 15;
    public const AMOUNT_EXCEEDS          = 16;
    public const DEPOSIT_EXCEEDS         = 17;
    public const REFUND_SUM_EXCEEDS      = 18;
    public const OBJECT_WRONG_STATE      = 20;

    // ── Authorization errors ─────────────────────────────────────────────────
    public const NOT_AUTHORIZED          = 25;
    public const TECHNICAL_AC_PROBLEM   = 26;
    public const WRONG_PAYMENT_TYPE      = 27;
    public const DECLINED_IN_3D          = 28;
    public const DECLINED_IN_AC          = 30;
    public const WRONG_DIGEST            = 31;
    public const EXPIRED_CARD            = 32;
    public const MASTER_NOT_AUTHORIZED   = 33;
    public const MASTER_NOT_VALID        = 34;
    public const SESSION_EXPIRED         = 35;
    public const BLACKLISTED_CARD        = 37;
    public const CARD_NOT_SUPPORTED      = 38;
    public const WATCHLISTED_CARD        = 39;
    public const DECLINED_FRAUD          = 40;
    public const DECLINED_TRA            = 46;
    public const CARDHOLDER_CANCELLED    = 50;

    // ── System errors ────────────────────────────────────────────────────────
    public const DUPLICATE_MESSAGE_ID    = 80;
    public const HSM_KEY_MISSING         = 82;
    public const CANCELLED_BY_ISSUER     = 83;
    public const DUPLICATE_VALUE         = 84;
    public const DECLINED_MERCHANT_RULES = 85;
    public const SOFT_DECLINE_SCA        = 86;

    // ── Push payment codes ───────────────────────────────────────────────────
    public const PUSH_NOT_FOUND          = 150;
    public const PUSH_EXPIRED            = 151;
    public const PUSH_ALREADY_PAID       = 152;
    public const PUSH_REVOKED            = 153;
    public const PUSH_WRONG_STATE        = 154;
    public const PUSH_NOT_ALLOWED        = 155;
    public const PUSH_ATTEMPTS_EXCEEDED  = 156;
    public const PUSH_INVALID_EXPIRY     = 160;
    public const PUSH_IN_PROGRESS        = 161;

    // ── ADDINFO / PSD2 ───────────────────────────────────────────────────────
    public const ADDITIONAL_INFO_REQ     = 200;

    // ── Session codes ────────────────────────────────────────────────────────
    public const PAYMENT_STILL_PENDING   = 250;
    public const SESSION_LOST            = 500;
    public const SESSION_RESTORED        = 501;
    public const UNEXPECTED_REQUEST      = 502;
    public const NO_SESSION_ID           = 503;

    // ── Technical ────────────────────────────────────────────────────────────
    public const TECHNICAL_PROBLEM       = 1000;
}
