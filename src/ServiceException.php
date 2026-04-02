<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay;

use Exception;
use Pronnect\GpWebPay\Response\MessageTrait;
use Pronnect\GpWebPayApi\Response\MessageInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class ServiceException
 */
class ServiceException extends Exception implements MessageInterface, SignedInterface
{
    use MessageTrait;
    use DigestTrait;
    use SignedTrait;

    protected ?string $primaryReturnCode = null;
    protected ?string $secondaryReturnCode = null;
    private ReturnCodeResolver $resolver;

    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->resolver = new ReturnCodeResolver();
    }

    /**
     * @param string $messageId
     *
     * @return $this
     */
    public function setMessageId(string $messageId)
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrimaryReturnCode(): ?string
    {
        return $this->primaryReturnCode;
    }

    /**
     * @param string|null $primaryReturnCode
     *
     * @return $this
     */
    public function setPrimaryReturnCode(?string $primaryReturnCode)
    {
        $this->primaryReturnCode = $primaryReturnCode;
        $this->code = (int) $primaryReturnCode;
        $this->message = $primaryReturnCode !== null
            ? $this->resolver->resolvePrimary((int) $primaryReturnCode)
            : sprintf('Unknown message for primaryReturnCode "%s"', $primaryReturnCode);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSecondaryReturnCode(): ?string
    {
        return $this->secondaryReturnCode;
    }

    /**
     * @param string|null $secondaryReturnCode
     *
     * @return $this
     */
    public function setSecondaryReturnCode(?string $secondaryReturnCode = null)
    {
        $this->secondaryReturnCode = $secondaryReturnCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->messageId ?? null,
            $this->primaryReturnCode ?? null,
            $this->secondaryReturnCode ?? null,
        ]);
    }

}
