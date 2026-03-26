<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPayApi\Response\CardOnFilePaymentFaultDetailInterface;

/**
 * Class CardOnFilePaymentFaultDetail
 *
 * SOAP fault detail for processCardOnFilePayment soft declines (PRCODE=46, SRCODE=300).
 * Caller accesses: $fault->detail->cardOnFilePaymentFaultDetail->authenticationLink
 */
class CardOnFilePaymentFaultDetail implements CardOnFilePaymentFaultDetailInterface
{
    // Public properties kept for direct SOAP deserialization by SoapClient.
    public ?string $messageId = null;
    public ?string $primaryReturnCode = null;
    public ?string $secondaryReturnCode = null;
    public ?string $authenticationLink = null;
    public ?string $signature = null;

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function getPrimaryReturnCode(): ?string
    {
        return $this->primaryReturnCode;
    }

    public function getSecondaryReturnCode(): ?string
    {
        return $this->secondaryReturnCode;
    }

    public function getAuthenticationLink(): ?string
    {
        return $this->authenticationLink;
    }
}
