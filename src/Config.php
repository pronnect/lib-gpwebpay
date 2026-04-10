<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay;

use Pronnect\GpWebPayApi\ConfigInterface;
use RuntimeException;

/**
 * Class Config
 */
class Config implements ConfigInterface
{
    protected const WS_URI_TEST = 'https://test.3dsecure.gpwebpay.com/pay-ws/v1/PaymentService';
    protected const WS_URI_PROD = 'https://3dsecure.gpwebpay.com/pay-ws/v1/PaymentService';

    private bool $isTestEnvironment;
    private ?string $provider;
    private ?string $merchantPrivateKeyPassword;
    private ?string $merchantPrivateKey;
    private ?string $merchantPrivateKeyPath;
    private ?string $merchantNumber;
    private ?string $GPEPublicKey;
    private ?string $GPEPublicKeyPath;
    private array $wsClientOptions;

    /**
     * Constructor Config
     *
     * @param array $configData
     */
    public function __construct(array $configData)
    {
        $this->isTestEnvironment = (bool)($configData['isTestEnvironment'] ?? true);
        $this->provider = (string)($configData['provider'] ?? null) ?: null;
        $this->merchantNumber = (string)($configData['merchantNumber'] ?? null) ?: null;
        $this->merchantPrivateKey = (string)($configData['merchantPrivateKey'] ?? null) ?: null;
        $this->merchantPrivateKeyPath = (string)($configData['merchantPrivateKeyPath'] ?? null) ?: null;
        $this->merchantPrivateKeyPassword = (string)($configData['merchantPrivateKeyPassword'] ?? null) ?: null;
        $this->GPEPublicKey = (string)($configData['GPEPublicKey'] ?? null) ?: null;
        $this->GPEPublicKeyPath = (string)($configData['GPEPublicKeyPath'] ?? null) ?: null;
        $this->wsClientOptions = (array)($configData['wsClientOptions'] ?? null) ?: [];
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * @return string
     */
    public function getWsUri(): string
    {
        return $this->isTestEnvironment
            ? self::WS_URI_TEST
            : self::WS_URI_PROD;
    }

    /**
     * @return array
     */
    public function getWsClientOptions(): array
    {
        return $this->wsClientOptions;
    }

    /**
     * @return string
     */
    public function getGPEPublicKey(): string
    {
        if ($this->GPEPublicKey === null) {
            if ($this->GPEPublicKeyPath === null) {
                $this->GPEPublicKeyPath = sprintf(
                    "%s/certs/gpe.signing_%s.pem",
                    dirname(__DIR__, 1),
                    $this->isTestEnvironment() ? "test" : "prod"
                );
            }
            if (!is_readable($this->GPEPublicKeyPath)) {
                throw new RuntimeException("Unable to get GPE public key from " . $this->GPEPublicKeyPath);
            }
            $publicKey = file_get_contents($this->GPEPublicKeyPath);
            if ($publicKey === false) {
                throw new RuntimeException("Unable to get GPE public key from " . $this->GPEPublicKeyPath);
            }
            $this->GPEPublicKey = $publicKey;
        }

        return $this->GPEPublicKey;
    }

    /**
     * @return bool
     */
    public function isTestEnvironment(): bool
    {
        return $this->isTestEnvironment;
    }

    /**
     * @return string
     */
    public function getMerchantNumber(): string
    {
        return $this->merchantNumber;
    }

    /**
     * @return string
     */
    public function getMerchantPrivateKey(): string
    {
        if ($this->merchantPrivateKey === null) {
            if (!is_readable($this->merchantPrivateKeyPath)) {
                throw new RuntimeException(
                    "Unable to get merchant private key from " . $this->merchantPrivateKeyPath
                );
            }
            $privateKey = file_get_contents($this->merchantPrivateKeyPath);
            if ($privateKey === false) {
                throw new RuntimeException(
                    "Unable to get merchant private key from " . $this->merchantPrivateKeyPath
                );
            }
            $this->merchantPrivateKey = $privateKey;
        }

        return $this->merchantPrivateKey;
    }

    /**
     * @return ?string
     */
    public function getMerchantPrivateKeyPassword(): ?string
    {
        return $this->merchantPrivateKeyPassword;
    }
}
