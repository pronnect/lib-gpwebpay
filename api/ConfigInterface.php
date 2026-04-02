<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi;

/**
 * Interface ConfigInterface
 *
 * @api
 */
interface ConfigInterface
{
    /**
     * @return bool
     */
    public function isTestEnvironment(): bool;

    /**
     * @return string
     */
    public function getProvider(): string;

    /**
     * @return string
     */
    public function getWsUri(): string;

    /**
     * @return array|null
     */
    public function getWsClientOptions(): ?array;

    /**
     * @return string
     */
    public function getGPEPublicKey(): string;

    /**
     * @return string
     */
    public function getMerchantNumber(): string;

    /**
     * @return string
     */
    public function getMerchantPrivateKey(): string;

    /**
     * @return string|null
     */
    public function getMerchantPrivateKeyPassword(): ?string;

}
