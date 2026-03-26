<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPayApi\Request\RequestInterface;
use RuntimeException;

trait RequestTrait
{
    private ?string $messageId = null;
    private ?string $provider = null;
    private ?string $merchantNumber = null;

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $method = "get" . ucfirst($name);
        if (!method_exists($this, $method)) {
            return null;
        }

        return $this->$method();
    }

    /**
     * @param $name
     * @param $value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $method = "set" . ucfirst($name);
        if (method_exists($this, $method)) {
            $this->$method($value);

            return;
        }

        if (property_exists($this, $name)) {
            $this->$name = $value;

            return;
        }
        
        throw new RuntimeException(
            sprintf(
                'Class "%s" does not have property "%s"',
                static::class,
                $name
            )
        );
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name): bool
    {
        return property_exists($this, $name) && !empty($this->$name);
    }

    /**
     * @return string|null
     */
    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    /**
     * @param string $messageId
     *
     * @return RequestInterface
     */
    public function setMessageId(string $messageId): RequestInterface
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     *
     * @return RequestInterface
     */
    public function setProvider(string $provider): RequestInterface
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMerchantNumber(): ?string
    {
        return $this->merchantNumber;
    }

    /**
     * @param string $merchantNumber
     *
     * @return RequestInterface
     */
    public function setMerchantNumber(string $merchantNumber): RequestInterface
    {
        $this->merchantNumber = $merchantNumber;

        return $this;
    }
}
