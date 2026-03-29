<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Exception;

/**
 * Thrown when a GP Webpay callback is missing required parameters (e.g. DIGEST).
 *
 * The callback should be rejected (HTTP 400).
 */
class InvalidCallbackException extends \RuntimeException
{
}
