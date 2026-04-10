<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Digest;

use Pronnect\GpWebPayApi\DigestInterface;

/**
 * Builds pipe-separated digest strings for GP Webpay HTTP API callback verification.
 *
 * Two digest types per spec kap. 5.2:
 *
 * - DIGEST  = response fields in spec order (19 fields, no MERCHANTNUMBER)
 * - DIGEST1 = same fields + MERCHANTNUMBER appended at the end
 *
 * MERCHANTNUMBER is NOT sent by GP Webpay in the callback — the merchant adds it
 * themselves when building the DIGEST1 verification string.
 *
 * This class is the single source of truth for response field order.
 * Both ReturnUrlVerifier and HttpGateway::processCallback() delegate here.
 *
 * ⚠️ Always verify using the ACTUALLY RECEIVED fields — never expected values.
 */
class HttpResponseDigest
{
    /**
     * DIGEST field order — response fields per spec kap. 5.2 (19 fields).
     */
    private const DIGEST_FIELD_ORDER = [
        'OPERATION',
        'ORDERNUMBER',
        'MERORDERNUM',
        'MD',
        'PRCODE',
        'SRCODE',
        'RESULTTEXT',
        'ADDINFO',
        'TOKEN',
        'EXPIRY',
        'ACSRES',
        'ACCODE',
        'PANPATTERN',
        'DAYTOCAPTURE',
        'TOKENREGSTATUS',
        'ACRC',
        'RRN',
        'PAR',
        'TRACEID',
    ];

    /**
     * DIGEST1 field order — same 19 fields + MERCHANTNUMBER at the end.
     * MERCHANTNUMBER is provided by the merchant (from config), not by GP Webpay.
     */
    private const DIGEST1_FIELD_ORDER = [
        'OPERATION',
        'ORDERNUMBER',
        'MERORDERNUM',
        'MD',
        'PRCODE',
        'SRCODE',
        'RESULTTEXT',
        'ADDINFO',
        'TOKEN',
        'EXPIRY',
        'ACSRES',
        'ACCODE',
        'PANPATTERN',
        'DAYTOCAPTURE',
        'TOKENREGSTATUS',
        'ACRC',
        'RRN',
        'PAR',
        'TRACEID',
        'MERCHANTNUMBER', // appended by merchant — not in GP Webpay's callback
    ];

    /**
     * Build the pipe-separated DIGEST verification string.
     *
     * @param array<string, string|null> $params  Raw callback parameters ($_GET or $_POST)
     */
    public function buildForDigest(array $params): string
    {
        return $this->buildFromOrder($params, self::DIGEST_FIELD_ORDER);
    }

    /**
     * Build the pipe-separated DIGEST1 verification string.
     *
     * Before calling this, add MERCHANTNUMBER to $params:
     *   $params['MERCHANTNUMBER'] = $this->config->getMerchantNumber();
     *
     * @param array<string, string|null> $params  Raw callback params + MERCHANTNUMBER
     */
    public function buildForDigest1(array $params): string
    {
        return $this->buildFromOrder($params, self::DIGEST1_FIELD_ORDER);
    }

    /**
     * @param array<string, string|null> $params
     * @param string[]                   $order
     */
    private function buildFromOrder(array $params, array $order): string
    {
        $parts = [];

        foreach ($order as $field) {
            $value = $params[$field] ?? null;
            if ($value !== null && (string) $value !== '') {
                $parts[] = (string) $value;
            }
        }

        return implode(DigestInterface::DIGEST_SEPARATOR, $parts);
    }

    /**
     * @return string[]
     */
    public static function getDigestFieldOrder(): array
    {
        return self::DIGEST_FIELD_ORDER;
    }

    /**
     * @return string[]
     */
    public static function getDigest1FieldOrder(): array
    {
        return self::DIGEST1_FIELD_ORDER;
    }
}
