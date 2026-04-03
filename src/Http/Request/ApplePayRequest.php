<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request;

use Pronnect\GpWebPay\Http\PayMethod;

/**
 * GP Webpay HTTP API — CREATE_ORDER request locked to Apple Pay.
 *
 * PAYMETHOD and PAYMETHODS are automatically set to APAY and cannot be changed.
 */
final class ApplePayRequest extends CardPaymentRequest
{
    public function __construct(
        int $orderNumber,
        int $amount,
        int $currency,
        int $depositFlag,
        string $url,
    ) {
        parent::__construct($orderNumber, $amount, $currency, $depositFlag, $url);
        parent::setPayMethod(PayMethod::APPLE_PAY);
        parent::setPayMethods(PayMethod::APPLE_PAY);
    }

    public function setPayMethod(string $payMethod): static
    {
        throw new \LogicException(
            'Cannot override PAYMETHOD on ApplePayRequest — it is locked to ' . PayMethod::APPLE_PAY . '.',
        );
    }

    public function setPayMethods(string $payMethods): static
    {
        throw new \LogicException(
            'Cannot override PAYMETHODS on ApplePayRequest — it is locked to ' . PayMethod::APPLE_PAY . '.',
        );
    }
}
