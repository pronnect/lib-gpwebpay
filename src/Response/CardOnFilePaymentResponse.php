<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\CardOnFilePaymentResponseInterface;
use Pronnect\GpWebPayApi\Response\MessageInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class CardOnFilePaymentResponse
 */
class CardOnFilePaymentResponse
    extends Response
    implements CardOnFilePaymentResponseInterface, MessageInterface, DigestInterface, SignedInterface
{
    use MessageTrait;
    use DigestTrait;
    use SignedTrait;

    protected ?string $authCode = null;
    protected ?string $tokenData = null;
    protected ?string $traceId = null;
    protected ?string $authResponseCode = null;
    protected ?string $authRRN = null;
    protected ?string $paymentAccountReference = null;

    /**
     * @return string|null
     */
    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }

    /**
     * @return string|null
     */
    public function getTokenData(): ?string
    {
        return $this->tokenData;
    }

    /**
     * @return string|null
     */
    public function getTraceId(): ?string
    {
        return $this->traceId;
    }

    /**
     * @return string|null
     */
    public function getAuthResponseCode(): ?string
    {
        return $this->authResponseCode;
    }

    /**
     * @return string|null
     */
    public function getAuthRRN(): ?string
    {
        return $this->authRRN;
    }

    /**
     * @return string|null
     */
    public function getPaymentAccountReference(): ?string
    {
        return $this->paymentAccountReference;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->getMessageId(),
            $this->authCode ?? null,
            $this->tokenData ?? null,
            $this->traceId ?? null,
            $this->authResponseCode ?? null,
            $this->authRRN ?? null,
            $this->paymentAccountReference ?? null,
        ]);
    }
}
