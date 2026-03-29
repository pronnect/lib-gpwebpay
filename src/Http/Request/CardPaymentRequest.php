<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Request;

use Pronnect\GpWebPay\Http\Exception\HttpRequestException;
use Pronnect\GpWebPay\Http\Operation;
use Pronnect\GpWebPayApi\Http\Request\HttpRequestInterface;

/**
 * GP Webpay HTTP API — CREATE_ORDER request.
 *
 * Initiates a card payment on the GP Webpay hosted payment page.
 * The gateway builds a redirect URL or POST form params, and the customer
 * is sent to the GP Webpay page to enter their card details.
 *
 * Field reference: GP Webpay HTTP API spec, kap. 5.1.
 */
class CardPaymentRequest implements HttpRequestInterface
{
    private string $operation = Operation::CREATE_ORDER;

    private int $orderNumber;
    private ?int $amount;
    private ?int $currency;
    private ?int $depositFlag;
    private string $url;

    private ?int $merOrderNum       = null;
    private ?string $description    = null;
    private ?string $md             = null;
    private ?string $userParam1     = null;
    private ?string $vrCode         = null;
    private ?int $fastPayId         = null;
    private ?string $payMethod      = null;
    private ?string $payMethods     = null;
    private ?string $email          = null;
    private ?string $referenceNumber = null;
    private ?string $addInfo        = null;
    private ?string $panPattern     = null;
    private ?string $token          = null;
    private ?string $fastToken      = null;
    private ?string $lang           = null;

    /**
     * @param int    $orderNumber  Unique payment number at the merchant (max 15 digits)
     * @param int    $amount       Amount in smallest currency units (haléře/cents); 0 is valid
     * @param int    $currency     ISO 4217 currency code (203=CZK, 978=EUR)
     * @param int    $depositFlag  DepositFlag::AUTHORIZE_ONLY or DepositFlag::IMMEDIATE_CAPTURE
     * @param string $url          Merchant callback URL (including protocol, max 300 chars)
     */
    public function __construct(
        int $orderNumber,
        int $amount,
        int $currency,
        int $depositFlag,
        string $url,
    ) {
        $this->orderNumber = $orderNumber;
        $this->amount      = $amount;
        $this->currency    = $currency;
        $this->depositFlag = $depositFlag;
        $this->url         = $url;
    }

    /**
     * Merchant order number — propagated to bank statement.
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

    /**
     * Payment description shown on the payment page (ASCII 0x20–0x7E only, max 255 chars).
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Merchant-defined data carried through the payment flow.
     *
     * Non-ASCII content is automatically BASE64-encoded as required by the spec.
     * The encoded value must not exceed 255 bytes.
     *
     * ⚠️ Must NOT contain personal data (GDPR).
     *
     * @throws HttpRequestException if the value exceeds 255 bytes after encoding
     */
    public function setMd(string $md): self
    {
        // Spec requires BASE64 encoding for non-ASCII content
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

    /**
     * COF / recurring registration code (UserParam1 constants).
     */
    public function setUserParam1(string $userParam1): self
    {
        $this->userParam1 = $userParam1;
        return $this;
    }

    /**
     * Encrypted VRCODE for bank account binding verification.
     * Use VrCodeEncryptor::encrypt() to generate the value.
     */
    public function setVrCode(string $vrCode): self
    {
        $this->vrCode = $vrCode;
        return $this;
    }

    /** Fastpay payment ID (required for Fastpay). */
    public function setFastPayId(int $fastPayId): self
    {
        $this->fastPayId = $fastPayId;
        return $this;
    }

    /**
     * Force a specific payment method (PayMethod constants).
     * Use PAYMETHODS to allow multiple methods.
     */
    public function setPayMethod(string $payMethod): self
    {
        $this->payMethod = $payMethod;
        return $this;
    }

    /**
     * Allowed payment methods as a comma-separated list of PayMethod constants.
     * Example: "GPAY,APAY,CTP"
     */
    public function setPayMethods(string $payMethods): self
    {
        $this->payMethods = $payMethods;
        return $this;
    }

    /** Customer email — must be a single valid email address. */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /** Internal reference number at the merchant (ASCII subset, max 20 chars). */
    public function setReferenceNumber(string $referenceNumber): self
    {
        $this->referenceNumber = $referenceNumber;
        return $this;
    }

    /**
     * PSD2 additional info XML (ADDINFO v5, max 24 000 chars).
     *
     * ⚠️ ADDINFO requires POST form submission — calling getRedirectUrl() with ADDINFO
     * set will throw HttpRequestException. Use getFormParams() instead.
     *
     * Use AddInfo::toXml() to build the value.
     */
    public function setAddInfo(string $addInfo): self
    {
        $this->addInfo = $addInfo;
        return $this;
    }

    /**
     * PAN mask pattern for token display (up to 10 masks, comma-separated).
     * Example: "405607*******0016"
     */
    public function setPanPattern(string $panPattern): self
    {
        $this->panPattern = $panPattern;
        return $this;
    }

    /** Card token for COF (card-on-file) payments. */
    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    /** Fasttoken for Fasttoken payments (required for Fasttoken). */
    public function setFastToken(string $fastToken): self
    {
        $this->fastToken = $fastToken;
        return $this;
    }

    /**
     * Language for the payment page (ISO 639-1, e.g. "CS", "SK", "EN").
     * Overrides HttpConfig::getDefaultLang() for this specific request.
     * LANG is NOT included in the digest — added after signing.
     */
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
     * Returns all non-null, non-empty fields for digest building and form params.
     * LANG and DIGEST are excluded — they are handled by HttpGateway.
     *
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
            'FASTPAYID'       => $this->fastPayId,
            'PAYMETHOD'       => $this->payMethod,
            'PAYMETHODS'      => $this->payMethods,
            'EMAIL'           => $this->email,
            'REFERENCENUMBER' => $this->referenceNumber,
            'ADDINFO'         => $this->addInfo,
            'PANPATTERN'      => $this->panPattern,
            'TOKEN'           => $this->token,
            'FASTTOKEN'       => $this->fastToken,
        ];

        // Remove null and empty-string values (but keep integer 0 — valid for AMOUNT/DEPOSITFLAG)
        return array_filter(
            $data,
            static fn ($value) => $value !== null && (string) $value !== '',
        );
    }
}
