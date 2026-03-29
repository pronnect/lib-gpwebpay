<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\MessageInterface;
use Pronnect\GpWebPayApi\Response\RecurringPaymentResponseInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class RecurringPaymentResponse
 *
 * Shared by recurring, usage-based subscription, regular subscription, prepaid,
 * payout, payout winning, payout insurance and mps express checkout responses —
 * all have the same field set: messageId + authCode + optional auth fields.
 */
class RecurringPaymentResponse
    extends Response
    implements RecurringPaymentResponseInterface, MessageInterface, DigestInterface, SignedInterface
{
    use MessageTrait;
    use DigestTrait;
    use SignedTrait;

    protected ?string $authCode = null;
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
            $this->traceId ?? null,
            $this->authResponseCode ?? null,
            $this->authRRN ?? null,
            $this->paymentAccountReference ?? null,
        ]);
    }
}
