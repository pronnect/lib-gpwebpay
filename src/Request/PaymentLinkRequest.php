<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\PaymentRequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;
use Pronnect\GpWebPay\Request\CardHolderData;

/**
 * Class PaymentLinkRequest
 */
class PaymentLinkRequest implements PaymentRequestInterface, SignedInterface
{
    use RequestTrait;
    use PaymentRequestTrait;
    use DigestTrait;
    use SignedTrait;

    private ?int $amount = null;
    private ?int $currencyCode = null;
    private ?bool $captureFlag = null;
    private ?string $orderNumber = null;
    private ?string $referenceNumber = null;
    private ?string $url = null;
    private ?string $description = null;
    private ?string $merchantData = null;
    private ?string $fastPayId = null;
    private ?string $defaultPayMethod = null;
    private ?string $disabledPayMethods = null;
    private ?string $payMethods = null;
    private ?string $email = null;
    private ?string $merchantEmail = null;
    private ?string $paymentExpiry = null;
    private ?string $language = null;
    private ?bool $registerRecurring = null;
    private ?bool $registerToken = null;
    private ?CardHolderData $cardHolderData = null;

    /**
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     *
     * @return $this
     */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCurrencyCode(): ?int
    {
        return $this->currencyCode;
    }

    /**
     * @param int $currencyCode
     *
     * @return $this
     */
    public function setCurrencyCode(int $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getCaptureFlag(): ?bool
    {
        return $this->captureFlag;
    }

    /**
     * @param bool $captureFlag
     *
     * @return $this
     */
    public function setCaptureFlag(bool $captureFlag): self
    {
        $this->captureFlag = $captureFlag;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    /**
     * @param string $orderNumber
     *
     * @return $this
     */
    public function setOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getReferenceNumber(): ?string
    {
        return $this->referenceNumber;
    }

    /**
     * @param string $referenceNumber
     *
     * @return $this
     */
    public function setReferenceNumber(string $referenceNumber): self
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMerchantData(): ?string
    {
        return $this->merchantData;
    }

    /**
     * @param string $merchantData
     *
     * @return $this
     */
    public function setMerchantData(string $merchantData): self
    {
        $this->merchantData = $merchantData;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFastPayId(): ?string
    {
        return $this->fastPayId;
    }

    /**
     * @param string $fastPayId
     *
     * @return $this
     */
    public function setFastPayId(string $fastPayId): self
    {
        $this->fastPayId = $fastPayId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDefaultPayMethod(): ?string
    {
        return $this->defaultPayMethod;
    }

    /**
     * @param string $defaultPayMethod
     *
     * @return $this
     */
    public function setDefaultPayMethod(string $defaultPayMethod): self
    {
        $this->defaultPayMethod = $defaultPayMethod;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDisabledPayMethods(): ?string
    {
        return $this->disabledPayMethods;
    }

    /**
     * @param string $disabledPayMethods
     *
     * @return $this
     */
    public function setDisabledPayMethods(string $disabledPayMethods): self
    {
        $this->disabledPayMethods = $disabledPayMethods;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPayMethods(): ?string
    {
        return $this->payMethods;
    }

    /**
     * @param string $payMethods
     *
     * @return $this
     */
    public function setPayMethods(string $payMethods): self
    {
        $this->payMethods = $payMethods;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMerchantEmail(): ?string
    {
        return $this->merchantEmail;
    }

    /**
     * @param string $merchantEmail
     *
     * @return $this
     */
    public function setMerchantEmail(string $merchantEmail): self
    {
        $this->merchantEmail = $merchantEmail;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaymentExpiry(): ?string
    {
        return $this->paymentExpiry;
    }

    /**
     * @param string $paymentExpiry
     *
     * @return $this
     */
    public function setPaymentExpiry(string $paymentExpiry): self
    {
        $this->paymentExpiry = $paymentExpiry;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getRegisterRecurring(): ?bool
    {
        return $this->registerRecurring;
    }

    /**
     * @param bool $registerRecurring
     *
     * @return $this
     */
    public function setRegisterRecurring(bool $registerRecurring): self
    {
        $this->registerRecurring = $registerRecurring;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getRegisterToken(): ?bool
    {
        return $this->registerToken;
    }

    /**
     * @param bool $registerToken
     *
     * @return $this
     */
    public function setRegisterToken(bool $registerToken): self
    {
        $this->registerToken = $registerToken;

        return $this;
    }

    /**
     * @return CardHolderData|null
     */
    public function getCardHolderData(): ?CardHolderData
    {
        return $this->cardHolderData;
    }

    /**
     * @param CardHolderData $cardHolderData
     *
     * @return $this
     */
    public function setCardHolderData(CardHolderData $cardHolderData): self
    {
        $this->cardHolderData = $cardHolderData;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->getMessageId(),
            $this->getProvider(),
            $this->getMerchantNumber(),
            $this->getPaymentNumber(),
            $this->amount ?? null,
            $this->currencyCode ?? null,
            (int)($this->captureFlag ?? null),
            $this->orderNumber ?? null,
            $this->referenceNumber ?? null,
            $this->url ?? null,
            $this->description ?? null,
            $this->merchantData ?? null,
            $this->fastPayId ?? null,
            $this->defaultPayMethod ?? null,
            $this->disabledPayMethods ?? null,
            $this->payMethods ?? null,
            $this->email ?? null,
            $this->merchantEmail ?? null,
            $this->paymentExpiry ?? null,
            $this->language ?? null,
            $this->registerRecurring !== null ? (int)$this->registerRecurring : null,
            $this->registerToken !== null ? (int)$this->registerToken : null,
            $this->cardHolderData?->getDigest(),
        ]);
    }
}
