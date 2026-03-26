<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface CardOnFilePaymentResponseInterface
 *
 * @api
 */
interface CardOnFilePaymentResponseInterface extends ResponseInterface
{
    /**
     * @return string|null
     */
    public function getAuthCode(): ?string;

    /**
     * @return string|null
     */
    public function getTokenData(): ?string;

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
}
