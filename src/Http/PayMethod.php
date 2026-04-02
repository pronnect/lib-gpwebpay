<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http;

/**
 * GP Webpay payment method codes (PAYMETHOD / PAYMETHODS parameters).
 *
 * Use PAYMETHOD to force a specific payment method.
 * Use PAYMETHODS to restrict which methods are offered (comma-separated list).
 *
 * Source: GP Webpay HTTP API spec, Príloha č. 5.
 */
class PayMethod
{
    // ── Digital wallets ─────────────────────────────────────────────────────
    /** Google Pay */
    public const GOOGLE_PAY   = 'GPAY';
    /** Apple Pay */
    public const APPLE_PAY    = 'APAY';

    // ── Payment buttons ──────────────────────────────────────────────────────
    /** Platba24 (Česká spořitelna) — requires a separate contract */
    public const PLATBA_24    = 'BTNCS';
    /** Click to Pay */
    public const CLICK_TO_PAY = 'CTP';

    // ── APM – GPE ────────────────────────────────────────────────────────────
    /** Twisto — buy now, pay later */
    public const TWISTO       = 'TWISTO';

    // ── APM – GP/PPRO (⚠️ being deprecated — will be replaced by GPE methods) ──
    /** Sofort */
    public const SOFORT       = 'SOFORT';
    /** EPS (Austria) */
    public const EPS          = 'EPS';
    /** paysafecard */
    public const PAYSAFECARD  = 'PAYSAFECARD';
    /** SEPA Direct Debit */
    public const SEPA_DEBIT   = 'SEPADIRECTDEBIT';
    /** Klarna */
    public const KLARNA       = 'KLARNA';
    /** PayPal */
    public const PAYPAL       = 'PAYPAL';
}
