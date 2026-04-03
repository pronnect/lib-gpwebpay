<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

trait MessageTrait
{
    protected ?string $messageId = null;


    /**
     * @param string $messageId
     *
     * @return void
     */
    public function setMessageId(string $messageId): void
    {
        $this->messageId = $messageId;
    }

    /**
     * @return string|null
     */
    public function getMessageId(): ?string
    {
        return $this->messageId;
    }
}
