<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request;

use Pronnect\GpWebPay\Http\PayMethod;

/**
 * GP Webpay HTTP API — CREATE_ORDER request locked to Google Pay.
 *
 * PAYMETHOD and PAYMETHODS are automatically set to GPAY and cannot be changed.
 */
final class GooglePayRequest extends CardPaymentRequest
{
    public function __construct(
        int $orderNumber,
        int $amount,
        int $currency,
        int $depositFlag,
        string $url,
    ) {
        parent::__construct($orderNumber, $amount, $currency, $depositFlag, $url);
        parent::setPayMethod(PayMethod::GOOGLE_PAY);
        parent::setPayMethods(PayMethod::GOOGLE_PAY);
    }

    public function setPayMethod(string $payMethod): static
    {
        throw new \LogicException(
            'Cannot override PAYMETHOD on GooglePayRequest — it is locked to ' . PayMethod::GOOGLE_PAY . '.',
        );
    }

    public function setPayMethods(string $payMethods): static
    {
        throw new \LogicException(
            'Cannot override PAYMETHODS on GooglePayRequest — it is locked to ' . PayMethod::GOOGLE_PAY . '.',
        );
    }
}
