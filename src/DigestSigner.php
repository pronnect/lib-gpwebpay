<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay;

use Pronnect\GpWebPayApi\DigestSignerInterface;
use RuntimeException;

class DigestSigner implements DigestSignerInterface
{
    private string $publicKey;
    private string $privateKey;
    private ?string $privateKeyPassword;

    /**
     * Constructor DigestSign
     *
     * @param string $publicKey
     * @param string $privateKey
     * @param string|null $privateKeyPassword
     */
    public function __construct(
        string $publicKey,
        string $privateKey,
        ?string $privateKeyPassword = null
    ) {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->privateKeyPassword = $privateKeyPassword ?: "";
    }

    /**
     * @param string $digest
     *
     * @return string
     */
    public function sign(string $digest): string
    {
        while (openssl_error_string() !== false) {
        }
        $primaryKey = openssl_pkey_get_private($this->privateKey, $this->privateKeyPassword);
        try {
            if ($primaryKey !== false) {
                $status = openssl_sign($digest, $signature, $primaryKey);
                if ($status === false) {
                    throw new RuntimeException(openssl_error_string() ?? 'Unable to sign data');
                }
            } else {
                throw new RuntimeException(openssl_error_string() ?? 'Invalid private key or password');
            }
        } finally {
            while (openssl_error_string() !== false) {
            }
        }

        return $signature;
    }

    /**
     * @param string $digest
     * @param string $signature
     *
     * @return bool
     */
    public function verify(string $digest, string $signature): bool
    {
        while (openssl_error_string() !== false) {
        }
        try {
            $publicKey = openssl_get_publickey($this->publicKey);
            $status = openssl_verify($digest, $signature, $publicKey);
            if ($status === -1) {
                throw new RuntimeException(openssl_error_string() ?? 'Unable to verify data');
            }
        } finally {
            while (openssl_error_string() !== false) {
            }
        }

        return $status === 1;
    }
}
