<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Http;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Http\VrCodeEncryptor;

/**
 * @covers \Pronnect\GpWebPay\Http\VrCodeEncryptor
 */
class VrCodeEncryptorTest extends TestCase
{
    /**
     * Test key: 16× 0x00 bytes.
     *
     * This matches the reference vector key confirmed by the user.
     * Production key is 16 bytes supplied by the bank — never use all-zeros in production.
     */
    private string $testKey;

    protected function setUp(): void
    {
        $this->testKey = str_repeat("\x00", 16);
    }

    // ─── Reference vectors (key = 16× 0x00, IV = 16× 0x00) ──────────────────
    //
    // Computed with: AES-128-CBC, PKCS5 padding, key = IV = 16 zero bytes.
    // Verified independently with Python cryptography library.
    //
    // When GP Webpay support provides an official test vector (plaintext + key + expected hex),
    // add it as a separate testOfficialReferenceVector() below.

    public function testReferenceVectorTest(): void
    {
        // "TEST" (4 bytes) → PKCS5-padded to 16 bytes → AES-128-CBC → 16 bytes encrypted → 32 hex chars
        $result = VrCodeEncryptor::encrypt('TEST', $this->testKey);
        $this->assertSame('B179802DFB94DE8AAA94D840CABBEC6A', $result);
    }

    public function testReferenceVectorHello(): void
    {
        // "Hello" (5 bytes) → PKCS5-padded to 16 bytes → 32 hex chars
        $result = VrCodeEncryptor::encrypt('Hello', $this->testKey);
        $this->assertSame('042DBE01027A650C746A5DC65DB6BE11', $result);
    }

    public function testReferenceVectorAbcdefgh(): void
    {
        // "ABCDEFGH" (8 bytes) → PKCS5-padded to 16 bytes → 32 hex chars
        $result = VrCodeEncryptor::encrypt('ABCDEFGH', $this->testKey);
        $this->assertSame('B8651A5CF33EBD5300CCDED395BB3C31', $result);
    }

    public function testReferenceVectorEmptyInput(): void
    {
        // "" (0 bytes) → PKCS5-padded to 16 bytes (all 0x10) → 32 hex chars
        $result = VrCodeEncryptor::encrypt('', $this->testKey);
        $this->assertSame('0143DB63EE66B0CDFF9F69917680151E', $result);
    }

    public function testReferenceVector22CharInput(): void
    {
        // 22× "A" → PKCS5-padded to 32 bytes (2 AES blocks) → 32 bytes encrypted → 64 hex chars
        // ⚠️ NOTE: 64 hex chars > VRCODE field max of 48 chars in the spec.
        // This means plaintext > 16 chars will overflow the VRCODE field.
        // Practical limit for VRCODE: max 16 chars plaintext → max 32 hex chars output.
        // TODO: confirm max plaintext length with GP Webpay support.
        $result = VrCodeEncryptor::encrypt(str_repeat('A', 22), $this->testKey);
        $this->assertSame('B49CBF19D357E6E1F6845C30FD5B63E35F88E264B6D471DC8E07D866B8B5143C', $result);
        $this->assertSame(64, strlen($result));
    }

    // ─── Output format tests ──────────────────────────────────────────────────

    public function testOutputIsUppercaseHex(): void
    {
        $result = VrCodeEncryptor::encrypt('TEST', $this->testKey);

        $this->assertMatchesRegularExpression(
            '/^[0-9A-F]+$/',
            $result,
            'Output must be uppercase hex (0-9 and A-F only)',
        );
    }

    public function testOutputLengthFor1To15CharInput(): void
    {
        // 1–15 chars → 1 AES block (16 encrypted bytes) → 32 hex chars
        // This is the safe range that fits within VRCODE max 48 chars
        foreach ([1, 8, 15] as $len) {
            $result = VrCodeEncryptor::encrypt(str_repeat('X', $len), $this->testKey);
            $this->assertSame(32, strlen($result), "{$len}-char input must produce 32 hex chars");
        }
    }

    public function testOutputLengthFor16To22CharInput(): void
    {
        // 16–22 chars → 2 AES blocks (32 encrypted bytes) → 64 hex chars
        // ⚠️ PKCS5: when plaintext is exactly 16 bytes (one AES block), a full padding block is added.
        // ⚠️ 64 hex chars exceeds VRCODE field max of 48 chars — confirm max with GP Webpay.
        // Practical safe limit: ≤15 chars plaintext → 32 hex chars output.
        foreach ([16, 17, 20, 22] as $len) {
            $result = VrCodeEncryptor::encrypt(str_repeat('X', $len), $this->testKey);
            $this->assertSame(64, strlen($result), "{$len}-char input must produce 64 hex chars");
        }
    }

    public function testOutputIsDeterministic(): void
    {
        $result1 = VrCodeEncryptor::encrypt('TEST_VRCODE', $this->testKey);
        $result2 = VrCodeEncryptor::encrypt('TEST_VRCODE', $this->testKey);

        $this->assertSame($result1, $result2);
    }

    public function testDifferentInputsProduceDifferentOutputs(): void
    {
        $result1 = VrCodeEncryptor::encrypt('ACCOUNT_A', $this->testKey);
        $result2 = VrCodeEncryptor::encrypt('ACCOUNT_B', $this->testKey);

        $this->assertNotSame($result1, $result2);
    }

    public function testDifferentKeysProduceDifferentOutputs(): void
    {
        $key2    = str_repeat("\x01", 16);
        $result1 = VrCodeEncryptor::encrypt('SAME_INPUT', $this->testKey);
        $result2 = VrCodeEncryptor::encrypt('SAME_INPUT', $key2);

        $this->assertNotSame($result1, $result2);
    }

    // ─── Input validation tests ───────────────────────────────────────────────

    public function testThrowsWhenInputExceeds22Characters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('22');

        VrCodeEncryptor::encrypt(str_repeat('X', 23), $this->testKey);
    }

    public function testDoesNotThrowWhenInputIsExactly22Characters(): void
    {
        $result = VrCodeEncryptor::encrypt(str_repeat('X', 22), $this->testKey);
        $this->assertNotEmpty($result);
    }

    public function testThrowsWhenKeyIsLessThan16Bytes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('16 bytes');

        VrCodeEncryptor::encrypt('TEST', str_repeat("\x00", 15));
    }

    public function testThrowsWhenKeyIsMoreThan16Bytes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('16 bytes');

        VrCodeEncryptor::encrypt('TEST', str_repeat("\x00", 17));
    }

    public function testDoesNotThrowWhenKeyIsExactly16Bytes(): void
    {
        $result = VrCodeEncryptor::encrypt('TEST', str_repeat("\x00", 16));
        $this->assertNotEmpty($result);
    }
}
