<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Request;

/**
 * Interface RequestInterface
 *
 * @api
 */
interface RequestInterface
{
    /**
     * @param string $messageId
     *
     * @return RequestInterface
     */
    public function setMessageId(string $messageId): RequestInterface;

    /**
     * @return string|null
     */
    public function getMessageId(): ?string;

    /**
     * @param string $provider
     *
     * @return RequestInterface
     */
    public function setProvider(string $provider): RequestInterface;

    /**
     * @return string|null
     */
    public function getProvider(): ?string;

    /**
     * @param string $merchantNumber
     *
     * @return RequestInterface
     */
    public function setMerchantNumber(string $merchantNumber): RequestInterface;

    /**
     * @return string|null
     */
    public function getMerchantNumber(): ?string;

}
