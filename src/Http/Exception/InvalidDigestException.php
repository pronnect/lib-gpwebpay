<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Exception;

/**
 * Thrown when DIGEST or DIGEST1 signature verification fails on a GP Webpay callback.
 *
 * This indicates either a tampered callback (potential attack) or a misconfigured
 * public key / merchant number. The callback should be rejected (HTTP 400).
 */
class InvalidDigestException extends \RuntimeException
{
}
