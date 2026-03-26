<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\PaymentRequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class RefundReverseRequest
 */
class RefundReverseRequest implements PaymentRequestInterface, SignedInterface
{
    use RequestTrait;
    use PaymentRequestTrait;
    use DigestTrait;
    use SignedTrait;

    private ?int $refundNumber = null;

    /**
     * @return int|null
     */
    public function getRefundNumber(): ?int
    {
        return $this->refundNumber;
    }

    /**
     * @param int $refundNumber
     *
     * @return $this
     */
    public function setRefundNumber(int $refundNumber): self
    {
        $this->refundNumber = $refundNumber;

        return $this;
    }

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
            $this->refundNumber ?? null,
        ]);
    }
}
