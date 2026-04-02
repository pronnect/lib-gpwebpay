<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Http\Response;

/**
 * Parsed GP Webpay HTTP API callback response.
 *
 * Populated from the return-URL parameters (GET or POST) after the customer
 * completes (or cancels/fails) the payment on the GP Webpay page.
 *
 * Field reference: GP Webpay HTTP API spec, kap. 5.2.
 *
 * @api
 */
interface HttpResponseInterface
{
    /** Payment operation type (e.g. "CREATE_ORDER", "CARD_VERIFICATION"). */
    public function getOperation(): string;

    /** Merchant order number (ORDERNUMBER from request). */
    public function getOrderNumber(): int;

    /** Merchant order number from MERORDERNUM (optional). */
    public function getMerOrderNum(): ?string;

    /** Merchant-defined data from MD parameter. May be BASE64-encoded if original contained non-ASCII. */
    public function getMd(): ?string;

    /**
     * Primary return code.
     * 0 = success. See ReturnCode constants for other values.
     */
    public function getPrCode(): int;

    /**
     * Secondary return code.
     * 0 = success. Provides additional context when PRCODE != 0.
     */
    public function getSrCode(): int;

    /** Human-readable result text. */
    public function getResultText(): ?string;

    /** Card token for subsequent COF payments (returned when USERPARAM1 was set). */
    public function getToken(): ?string;

    /** Card expiry date in YYMM format (e.g. "2612" for December 2026). */
    public function getExpiry(): ?string;

    /**
     * 3DS authentication result:
     * N = not authenticated, A = attempted, F = failure, D = decoupled, E = error.
     */
    public function getAcsRes(): ?string;

    /** Authorization code from the card issuer. */
    public function getAcCode(): ?string;

    /** PAN pattern (masked card number, e.g. "405607*******0016"). */
    public function getPanPattern(): ?string;

    /** Day of planned capture in DDMMYYYY format. */
    public function getDayToCapture(): ?string;

    /** Token registration status ("SUCCESS" or "EXISTOWNER"). */
    public function getTokenRegStatus(): ?string;

    /** Authorization response code. */
    public function getAcrc(): ?string;

    /** Retrieval reference number. */
    public function getRrn(): ?string;

    /** Payment account reference. */
    public function getPar(): ?string;

    /** Trace ID for transaction tracking. */
    public function getTraceId(): ?string;

    /**
     * Returns true when PRCODE === 0 and SRCODE === 0 (payment successful).
     */
    public function isSuccess(): bool;
}
