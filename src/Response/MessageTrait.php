<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

trait MessageTrait
{
    protected ?string $messageId = null;

    /**
     * @return string|null
     */
    public function getMessageId(): ?string
    {
        return $this->messageId;
    }
}
