<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\ServiceException;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Response\CardOnFilePaymentFaultDetailInterface;

/**
 * Class CardOnFilePaymentFaultDetail
 *
 * SOAP fault detail for processCardOnFilePayment soft declines (PRCODE=46, SRCODE=300).
 * Caller accesses: $fault->detail->cardOnFilePaymentFaultDetail->authenticationLink
 */
class CardOnFilePaymentFaultDetail extends ServiceException implements CardOnFilePaymentFaultDetailInterface
{
    use MessageTrait;
    use DigestTrait;
    use SignedTrait;

    protected ?string $authenticationLink = null;

    /**
     * @return string|null
     */
    public function getAuthenticationLink(): ?string
    {
        return $this->authenticationLink;
    }

    /**
     * @param string|null $authenticationLink
     *
     * @return void
     */
    public function setAuthenticationLink(?string $authenticationLink = null): void
    {
        $this->authenticationLink = $authenticationLink;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->messageId,
            $this->primaryReturnCode,
            $this->secondaryReturnCode,
            $this->authenticationLink,
        ]);
    }
}
