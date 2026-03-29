<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Digest;

use Pronnect\GpWebPayApi\DigestInterface;

/**
 * Builds the pipe-separated digest string for GP Webpay HTTP API requests.
 *
 * Field order is defined by the GP Webpay HTTP API specification (chapter 3.1 / 5.1).
 * Only non-null and non-empty fields are included; order is always fixed.
 *
 * ⚠️ LANG is intentionally NOT in this list — it must be added to params AFTER signing.
 * ⚠️ DIGEST is NOT in this list — it is the result, not an input to the hash.
 * ⚠️ AMOUNT = 0 is a valid value (e.g. CARD_VERIFICATION) — use (string) cast, not loose comparison.
 */
class HttpRequestDigest
{
    /**
     * Field order for the request digest string, per spec kap. 3.1 / 5.1.
     */
    private const FIELD_ORDER = [
        'MERCHANTNUMBER',
        'OPERATION',
        'ORDERNUMBER',
        'AMOUNT',
        'CURRENCY',
        'DEPOSITFLAG',
        'MERORDERNUM',
        'URL',
        'DESCRIPTION',
        'MD',
        'USERPARAM1',
        'VRCODE',
        'FASTPAYID',
        'PAYMETHOD',
        'PAYMETHODS',
        'EMAIL',
        'REFERENCENUMBER',
        'ADDINFO',
        'PANPATTERN',
        'TOKEN',
        'FASTTOKEN',
        // LANG — not in digest, added after signing
        // DIGEST — result, never included
    ];

    /**
     * Build the pipe-separated digest string from the given parameters.
     *
     * Null and empty-string values are excluded. Numeric 0 (e.g. AMOUNT=0) is included.
     *
     * @param array<string, mixed> $params
     */
    public function build(array $params): string
    {
        $parts = [];

        foreach (self::FIELD_ORDER as $field) {
            $value = $params[$field] ?? null;
            // Use string cast: keeps numeric 0, excludes null and empty string
            if ($value !== null && (string) $value !== '') {
                $parts[] = (string) $value;
            }
        }

        return implode(DigestInterface::DIGEST_SEPARATOR, $parts);
    }

    /**
     * Returns the canonical field order. Useful for documentation / debugging.
     *
     * @return string[]
     */
    public static function getFieldOrder(): array
    {
        return self::FIELD_ORDER;
    }
}
