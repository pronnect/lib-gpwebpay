<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay;

use DOMDocument;
use DOMXPath;
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
    protected ?DOMXPath $domCodes = null;

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
        $this->code = $primaryReturnCode;
        $this->message = $this->resolveMessage($primaryReturnCode);

        return $this;
    }

    /**
     * @param $primaryReturnCode
     *
     * @return string
     */
    private function resolveMessage($primaryReturnCode = null): string
    {
        $message = sprintf("Unknown message for primaryReturnCode \"%s\"", $primaryReturnCode);
        if ($this->domCodes === null) {
            $codes = dirname(__DIR__) . '/resources/xml/codes.xml';
            $xml = is_readable($codes) ? file_get_contents($codes) : false;
            if ($xml !== false) {
                $dom = new DOMDocument();
                if ($dom->loadXML($xml)) {
                    $this->domCodes = new DOMXPath($dom);
                }
            }
        }

        if ($this->domCodes === null) {
            return $message;
        }

        $query = sprintf('/codes/primaryReturnCodes/code[@value="%s"]', $primaryReturnCode);
        $node = $this->domCodes->query($query);
        if ($node->count()) {
            $message = $node->item(0)->textContent;
        }

        return $message;
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
