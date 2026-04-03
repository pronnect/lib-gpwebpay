<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi;

/**
 * Interface ReturnUrlVerifierInterface
 *
 * Verifies the DIGEST and DIGEST1 signatures on the HTTP redirect callback that
 * GP Webpay sends to the merchant's return URL after a payment attempt.
 *
 * DIGEST field order (null/empty fields excluded) — 19 response fields per spec kap. 5.2:
 *   OPERATION | ORDERNUMBER | MERORDERNUM | MD | PRCODE | SRCODE | RESULTTEXT |
 *   ADDINFO | TOKEN | EXPIRY | ACSRES | ACCODE | PANPATTERN | DAYTOCAPTURE |
 *   TOKENREGSTATUS | ACRC | RRN | PAR | TRACEID
 *
 * DIGEST1 = same 19 fields + MERCHANTNUMBER appended at the end.
 * MERCHANTNUMBER is not sent by GP Webpay — the merchant adds it from config.
 *
 * @api
 */
interface ReturnUrlVerifierInterface
{
    /**
     * Verify the DIGEST (and DIGEST1 if present) signatures on the callback.
     *
     * - DIGEST is optional but verified if present
     * - DIGEST1 is required and always verified
     *
     * @param array<string,string|null> $params  Raw $_GET or $_POST callback parameters.
     *
     * @return bool  True when all present signatures are valid.
     */
    public function verify(array $params): bool;
}
