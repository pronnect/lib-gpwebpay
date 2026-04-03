<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\AdditionalInfoResponseInterface;
use Pronnect\GpWebPayApi\Response\PaymentDetailResponseInterface;
use Pronnect\GpWebPayApi\Response\SimpleValueInterface;

/**
 * Class PaymentDetailResponse
 */
class PaymentDetailResponse
    extends StateResponse
    implements PaymentDetailResponseInterface
{
    use MessageTrait;
    use DigestTrait;
    use SignedTrait;

    protected ?string $paymentMethod = null;
    protected ?string $panMasked = null;
    protected ?string $brandName = null;
    protected ?string $paymentAmount = null;
    protected ?string $approveAmount = null;
    protected ?string $captureAmount = null;
    protected ?string $refundAmount = null;
    protected ?string $approveCode = null;
    protected ?string $paymentTime = null;
    protected ?string $approveTime = null;
    protected ?string $lastCaptureTime = null;
    protected ?AdditionalInfoResponseInterface $additionalInfo = null;
    /** @var SimpleValue[] */
    protected array $simpleValueHolder = [];
    protected ?string $panToken = null;
    protected ?string $panPattern = null;
    protected ?string $panExpiry = null;
    protected ?string $acsResult = null;
    protected ?string $dayToCapture = null;
    protected ?string $traceId = null;
    protected ?string $authResponseCode = null;
    protected ?string $authRRN = null;
    protected ?string $paymentAccountReference = null;
    protected ?string $iasId = null;
    protected ?string $payPalId = null;

    /**
     * @return string|null
     */
    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    /**
     * @return string|null
     */
    public function getPanMasked(): ?string
    {
        return $this->panMasked;
    }

    /**
     * @return string|null
     */
    public function getBrandName(): ?string
    {
        return $this->brandName;
    }

    /**
     * @return string|null
     */
    public function getPaymentAmount(): ?string
    {
        return $this->paymentAmount;
    }

    /**
     * @return string|null
     */
    public function getApproveAmount(): ?string
    {
        return $this->approveAmount;
    }

    /**
     * @return string|null
     */
    public function getCaptureAmount(): ?string
    {
        return $this->captureAmount;
    }

    /**
     * @return string|null
     */
    public function getRefundAmount(): ?string
    {
        return $this->refundAmount;
    }

    /**
     * @return string|null
     */
    public function getApproveCode(): ?string
    {
        return $this->approveCode;
    }

    /**
     * @return string|null
     */
    public function getPaymentTime(): ?string
    {
        return $this->paymentTime;
    }

    /**
     * @return string|null
     */
    public function getApproveTime(): ?string
    {
        return $this->approveTime;
    }

    /**
     * @return string|null
     */
    public function getLastCaptureTime(): ?string
    {
        return $this->lastCaptureTime;
    }

    /**
     * @return SimpleValue[]
     */
    public function getSimpleValueHolder(): array
    {
        return $this->simpleValueHolder;
    }

    /**
     * @return string|null
     */
    public function getPanToken(): ?string
    {
        return $this->panToken;
    }

    /**
     * @return string|null
     */
    public function getPanPattern(): ?string
    {
        return $this->panPattern;
    }

    /**
     * @return string|null
     */
    public function getPanExpiry(): ?string
    {
        return $this->panExpiry;
    }

    /**
     * @return string|null
     */
    public function getAcsResult(): ?string
    {
        return $this->acsResult;
    }

    /**
     * @return string|null
     */
    public function getDayToCapture(): ?string
    {
        return $this->dayToCapture;
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
    public function getIasId(): ?string
    {
        return $this->iasId;
    }

    /**
     * @return string|null
     */
    public function getPayPalId(): ?string
    {
        return $this->payPalId;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        $simpleValueDigest = implode(
            DigestInterface::DIGEST_SEPARATOR,
            array_map(
                static fn(SimpleValueInterface $simpleValue) => $simpleValue->getDigest(),
                $this->simpleValueHolder
            )
        );

        return $this->makeDigest(
            [
                $this->getMessageId(),
                $this->getState(),
                $this->getStatus(),
                $this->getSubStatus(),
                $this->paymentMethod ?? null,
                $this->panMasked ?? null,
                $this->brandName ?? null,
                $this->paymentAmount ?? null,
                $this->approveAmount ?? null,
                $this->captureAmount ?? null,
                $this->refundAmount ?? null,
                $this->approveCode ?? null,
                $this->paymentTime ?? null,
                $this->approveTime ?? null,
                $this->lastCaptureTime ?? null,
                $this->getAdditionalInfo() instanceof DigestInterface
                    ? $this->getAdditionalInfo()->getDigest()
                    : null,
                $simpleValueDigest ?: null,
                $this->panToken ?? null,
                $this->panPattern ?? null,
                $this->panExpiry ?? null,
                $this->acsResult ?? null,
                $this->dayToCapture ?? null,
                $this->traceId ?? null,
                $this->authResponseCode ?? null,
                $this->authRRN ?? null,
                $this->paymentAccountReference ?? null,
                $this->iasId ?? null,
                $this->payPalId ?? null,
            ]
        );
    }

    /**
     * @return AdditionalInfoResponse|null
     */
    public function getAdditionalInfo(): ?AdditionalInfoResponseInterface
    {
        return $this->additionalInfo;
    }
}
