<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface PaymentDetailResponseInterface
 *
 * @api
 */
interface PaymentDetailResponseInterface
{
    /**
     * @return string|null
     */
    public function getPaymentMethod(): ?string;

    /**
     * @return string|null
     */
    public function getPanMasked(): ?string;

    /**
     * @return string|null
     */
    public function getBrandName(): ?string;

    /**
     * @return string|null
     */
    public function getPaymentAmount(): ?string;

    /**
     * @return string|null
     */
    public function getApproveAmount(): ?string;

    /**
     * @return string|null
     */
    public function getCaptureAmount(): ?string;

    /**
     * @return string|null
     */
    public function getRefundAmount(): ?string;

    /**
     * @return string|null
     */
    public function getApproveCode(): ?string;

    /**
     * @return string|null
     */
    public function getPaymentTime(): ?string;

    /**
     * @return string|null
     */
    public function getApproveTime(): ?string;

    /**
     * @return string|null
     */
    public function getLastCaptureTime(): ?string;

    /**
     * @return AdditionalInfoResponseInterface|null
     */
    public function getAdditionalInfo(): ?AdditionalInfoResponseInterface;

    /**
     * @return string|null
     */
    public function getPanToken(): ?string;

    /**
     * @return string|null
     */
    public function getPanPattern(): ?string;

    /**
     * @return string|null
     */
    public function getPanExpiry(): ?string;

    /**
     * @return string|null
     */
    public function getAcsResult(): ?string;

    /**
     * @return string|null
     */
    public function getDayToCapture(): ?string;

    /**
     * @return string|null
     */
    public function getTraceId(): ?string;

    /**
     * @return string|null
     */
    public function getAuthResponseCode(): ?string;

    /**
     * @return string|null
     */
    public function getAuthRRN(): ?string;

    /**
     * @return string|null
     */
    public function getPaymentAccountReference(): ?string;

    /**
     * @return string|null
     */
    public function getIasId(): ?string;

    /**
     * @return string|null
     */
    public function getPayPalId(): ?string;

}
