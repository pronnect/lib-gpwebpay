<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Exception;

/**
 * Thrown when an HTTP request cannot be built due to invalid parameter combinations.
 *
 * Examples:
 * - ADDINFO is set on a GET redirect request (ADDINFO requires POST)
 * - MD field exceeds 255 bytes after encoding
 */
class HttpRequestException extends \RuntimeException
{
}
