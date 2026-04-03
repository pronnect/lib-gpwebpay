<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay;

/**
 * Class PushPaymentStatus
 */
class PushPaymentStatus
{
    public const STATUS_CREATED   = 'CR';
    public const STATUS_EXPIRED   = 'EX';
    public const STATUS_CANCELED  = 'CA';
    public const STATUS_BLOCKED   = 'BL';
    public const STATUS_PROCESSED = 'PR';

}
