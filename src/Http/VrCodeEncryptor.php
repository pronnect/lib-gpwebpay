<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http;

/**
 * Encrypts the VRCODE parameter for GP Webpay bank account binding verification.
 *
 * Spec: GP Webpay HTTP API, kap. 9.6.
 *
 * Algorithm:
 *   1. Plaintext: max 22 characters before encryption
 *   2. Cipher: AES-128-CBC
 *   3. Key: 16 bytes (AES-128) — provided by the bank
 *   4. IV: 16 zero bytes (0x00 × 16)
 *   5. Padding: PKCS5 (same as PKCS7, OpenSSL default for CBC)
 *   6. Output: uppercase hex (each byte → 2 hex chars, 0–9, A–F)
 *              Max output length: 48 chars (fits in VRCODE max 48)
 *
 * Usage:
 *   $encrypted = VrCodeEncryptor::encrypt('ACCOUNT_NUMBER', $aesKey16bytes);
 *   $request->setVrCode($encrypted);
 *
 * ⚠️ Obtain the AES key from your GP Webpay bank account manager.
 * ⚠️ Request a test vector from GP Webpay support to verify your integration
 *    (specific plaintext → expected hex output). Update testReferenceVector() in
 *    VrCodeEncryptorTest once you have it.
 */
class VrCodeEncryptor
{
    private const CIPHER     = 'AES-128-CBC';
    private const KEY_LENGTH = 16; // bytes — AES-128
    private const IV         = "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"; // 16 × 0x00

    /**
     * Encrypts VRCODE for use in the GP Webpay HTTP API request.
     *
     * @param string $vrcode  Plaintext VRCODE, max 22 characters before encryption
     * @param string $aesKey  16-byte AES-128 key from the bank (raw binary, NOT hex)
     *
     * @return string  Uppercase hex-encoded encrypted VRCODE (max 48 chars)
     *
     * @throws \InvalidArgumentException if vrcode exceeds 22 characters
     * @throws \InvalidArgumentException if aesKey is not exactly 16 bytes
     * @throws \RuntimeException         if OpenSSL encryption fails
     */
    public static function encrypt(string $vrcode, string $aesKey): string
    {
        if (strlen($vrcode) > 22) {
            throw new \InvalidArgumentException(
                sprintf(
                    'VRCODE must not exceed 22 characters before encryption, got %d: "%s"',
                    strlen($vrcode),
                    $vrcode,
                ),
            );
        }

        if (strlen($aesKey) !== self::KEY_LENGTH) {
            throw new \InvalidArgumentException(
                sprintf(
                    'AES key must be exactly %d bytes (AES-128), got %d bytes',
                    self::KEY_LENGTH,
                    strlen($aesKey),
                ),
            );
        }

        $encrypted = openssl_encrypt($vrcode, self::CIPHER, $aesKey, OPENSSL_RAW_DATA, self::IV);

        if ($encrypted === false) {
            throw new \RuntimeException(
                'VrCodeEncryptor: openssl_encrypt failed: ' . (openssl_error_string() ?: 'unknown error'),
            );
        }

        return strtoupper(bin2hex($encrypted));
    }
}
