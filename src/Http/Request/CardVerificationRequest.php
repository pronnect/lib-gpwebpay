<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request;

use Pronnect\GpWebPay\Http\Exception\HttpRequestException;
use Pronnect\GpWebPay\Http\Operation;
use Pronnect\GpWebPayApi\Http\Request\HttpRequestInterface;

/**
 * GP Webpay HTTP API — CARD_VERIFICATION request.
 *
 * Verifies card validity without blocking funds. Primarily used for:
 * - COF (card-on-file) registration (USERPARAM1 = 'T' or 'S')
 * - Recurring payment master registration (USERPARAM1 = 'R')
 *
 * AMOUNT, CURRENCY, and DEPOSITFLAG are optional (unlike CardPaymentRequest).
 *
 * ⚠️ COF registration must use CARD_VERIFICATION, NOT a minimum-amount payment + reversal.
 * Card associations (Visa/MC) prohibited the latter since 2021.
 *
 * Field reference: GP Webpay HTTP API spec, kap. 6.
 */
class CardVerificationRequest implements HttpRequestInterface
{
    private string $operation = Operation::CARD_VERIFICATION;

    private int $orderNumber;
    private string $url;

    private ?int $amount            = null;
    private ?int $currency          = null;
    private ?int $depositFlag       = null;
    private ?int $merOrderNum       = null;
    private ?string $description    = null;
    private ?string $md             = null;
    private ?string $userParam1     = null;
    private ?string $vrCode         = null;
    private ?string $payMethod      = null;
    private ?string $payMethods     = null;
    private ?string $email          = null;
    private ?string $referenceNumber = null;
    private ?string $addInfo        = null;
    private ?string $panPattern     = null;
    private ?string $token          = null;
    private ?string $lang           = null;

    /**
     * @param int    $orderNumber Unique verification number at the merchant
     * @param string $url         Merchant callback URL (including protocol)
     */
    public function __construct(
        int $orderNumber,
        string $url,
    ) {
        $this->orderNumber = $orderNumber;
        $this->url         = $url;
    }

    /** Amount in smallest currency units (optional for card verification). */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /** ISO 4217 currency code (optional for card verification). */
    public function setCurrency(int $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /** Deposit flag (optional for card verification). */
    public function setDepositFlag(int $depositFlag): self
    {
        $this->depositFlag = $depositFlag;
        return $this;
    }

    /**
     * Merchant order number.
     * ⚠️ Bank only displays the first 16 digits — longer values are silently truncated.
     */
    public function setMerOrderNum(int $merOrderNum): self
    {
        if (strlen((string) $merOrderNum) > 16) {
            trigger_error(
                sprintf(
                    'MERORDERNUM "%s" is longer than 16 digits — bank will display only the first 16',
                    $merOrderNum,
                ),
                E_USER_WARNING,
            );
        }
        $this->merOrderNum = $merOrderNum;
        return $this;
    }

    /** Payment description (ASCII 0x20–0x7E only, max 255 chars). */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Merchant-defined data (non-ASCII auto-encoded to BASE64).
     * ⚠️ Must NOT contain personal data (GDPR).
     *
     * @throws HttpRequestException if value exceeds 255 bytes after encoding
     */
    public function setMd(string $md): self
    {
        if (!mb_check_encoding($md, 'ASCII') || preg_match('/[^\x20-\x7E]/', $md)) {
            $md = base64_encode($md);
        }
        if (strlen($md) > 255) {
            throw new HttpRequestException(
                sprintf('MD field exceeds 255 bytes after encoding (got %d bytes)', strlen($md)),
            );
        }
        $this->md = $md;
        return $this;
    }

    /** COF / recurring registration code (UserParam1 constants). */
    public function setUserParam1(string $userParam1): self
    {
        $this->userParam1 = $userParam1;
        return $this;
    }

    /** Encrypted VRCODE — use VrCodeEncryptor::encrypt() to generate. */
    public function setVrCode(string $vrCode): self
    {
        $this->vrCode = $vrCode;
        return $this;
    }

    /** Force a specific payment method (PayMethod constants). */
    public function setPayMethod(string $payMethod): self
    {
        $this->payMethod = $payMethod;
        return $this;
    }

    /** Allowed payment methods (comma-separated PayMethod constants). */
    public function setPayMethods(string $payMethods): self
    {
        $this->payMethods = $payMethods;
        return $this;
    }

    /** Customer email address. */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /** Internal reference number (ASCII subset, max 20 chars). */
    public function setReferenceNumber(string $referenceNumber): self
    {
        $this->referenceNumber = $referenceNumber;
        return $this;
    }

    /**
     * PSD2 additional info XML (AddInfo::toXml()).
     * ⚠️ Requires POST form submission — not compatible with getRedirectUrl().
     */
    public function setAddInfo(string $addInfo): self
    {
        $this->addInfo = $addInfo;
        return $this;
    }

    /** PAN mask pattern (up to 10 masks, comma-separated). */
    public function setPanPattern(string $panPattern): self
    {
        $this->panPattern = $panPattern;
        return $this;
    }

    /** Card token for COF payments. */
    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    /** Language for the payment page (ISO 639-1, e.g. "CS"). Not included in digest. */
    public function setLang(string $lang): self
    {
        $this->lang = strtoupper($lang);
        return $this;
    }

    public function getAddInfo(): ?string
    {
        return $this->addInfo;
    }

    public function getLang(): ?string
    {
        return $this->lang;
    }

    /**
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        $data = [
            'OPERATION'       => $this->operation,
            'ORDERNUMBER'     => $this->orderNumber,
            'AMOUNT'          => $this->amount,
            'CURRENCY'        => $this->currency,
            'DEPOSITFLAG'     => $this->depositFlag,
            'MERORDERNUM'     => $this->merOrderNum,
            'URL'             => $this->url,
            'DESCRIPTION'     => $this->description,
            'MD'              => $this->md,
            'USERPARAM1'      => $this->userParam1,
            'VRCODE'          => $this->vrCode,
            'PAYMETHOD'       => $this->payMethod,
            'PAYMETHODS'      => $this->payMethods,
            'EMAIL'           => $this->email,
            'REFERENCENUMBER' => $this->referenceNumber,
            'ADDINFO'         => $this->addInfo,
            'PANPATTERN'      => $this->panPattern,
            'TOKEN'           => $this->token,
        ];

        return array_filter(
            $data,
            static fn ($value) => $value !== null && (string) $value !== '',
        );
    }
}
