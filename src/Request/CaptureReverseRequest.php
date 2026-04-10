<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\PaymentRequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class CaptureReverseRequest
 */
class CaptureReverseRequest implements PaymentRequestInterface, SignedInterface
{
    use RequestTrait;
    use PaymentRequestTrait;
    use DigestTrait;
    use SignedTrait;

    private ?int $captureNumber = null;

    /**
     * @return int|null
     */
    public function getCaptureNumber(): ?int
    {
        return $this->captureNumber;
    }

    /**
     * @param int $captureNumber
     *
     * @return $this
     */
    public function setCaptureNumber(int $captureNumber): self
    {
        $this->captureNumber = $captureNumber;

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
            $this->captureNumber ?? null,
        ]);
    }
}
