<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\PaymentRequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class PaymentDeleteRequest
 */
class PaymentDeleteRequest implements PaymentRequestInterface, SignedInterface
{
    use RequestTrait;
    use PaymentRequestTrait;
    use DigestTrait;
    use SignedTrait;

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->messageId ?? null,
            $this->provider ?? null,
            $this->merchantNumber ?? null,
            $this->paymentNumber ?? null,
        ]);
    }
}
