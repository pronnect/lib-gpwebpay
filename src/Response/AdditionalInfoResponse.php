<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\AdditionalInfoResponseInterface;
use Pronnect\GpWebPayApi\Response\AddressDetailsInterface;
use Pronnect\GpWebPayApi\Response\CardDetailInterface;
use Pronnect\GpWebPayApi\Response\ContactInterface;
use Pronnect\GpWebPayApi\Response\LoyaltyProgramDetailsInterface;

/**
 * Class AdditionalInfoResponse
 */
class AdditionalInfoResponse
    extends Response
    implements AdditionalInfoResponseInterface, DigestInterface
{
    use DigestTrait;

    protected ?string $walletDetails = null;
    protected ?ContactInterface $contact = null;
    protected ?AddressDetailsInterface $billingDetails = null;
    protected ?AddressDetailsInterface $shippingDetails = null;
    /**
     * @var CardDetailInterface[]|null
     */
    protected array $cardDetails = [];
    protected ?LoyaltyProgramDetailsInterface $loyaltyProgramDetails = null;

    /**
     * @return string|null
     */
    public function getWalletDetails(): ?string
    {
        return $this->walletDetails;
    }

    /**
     * @return CardDetail[]
     */
    public function getCardDetails(): array
    {
        return $this->cardDetails;
    }

    /**
     * @return Contact|null
     */
    public function getContact(): ?ContactInterface
    {
        return $this->contact;
    }

    /**
     * @return AddressDetails|null
     */
    public function getBillingDetails(): ?AddressDetailsInterface
    {
        return $this->billingDetails;
    }

    /**
     * @return AddressDetails|null
     */
    public function getShippingDetails(): ?AddressDetailsInterface
    {
        return $this->shippingDetails;
    }

    /**
     * @return LoyaltyProgramDetails|null
     */
    public function getLoyaltyProgramDetails(): ?LoyaltyProgramDetailsInterface
    {
        return $this->loyaltyProgramDetails;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        $cardDetailsDigest = implode(
            DigestInterface::DIGEST_SEPARATOR,
            array_map(
                static fn(DigestInterface $cardDetail) => $cardDetail->getDigest(),
                array_filter(
                    $this->cardDetails,
                    static fn(CardDetailInterface $cardDetail) => $cardDetail instanceof DigestInterface
                )
            )
        );

        return $this->makeDigest(
            [
                $this->walletDetails ?? null,
                $this->getContact() instanceof DigestInterface
                    ? $this->getContact()->getDigest()
                    : null,
                $this->getBillingDetails() instanceof DigestInterface ?
                    $this->getBillingDetails()->getDigest()
                    : null,
                $this->getShippingDetails() instanceof DigestInterface
                    ? $this->getShippingDetails()->getDigest()
                    : null,
                $cardDetailsDigest ?: null,
                $this->getLoyaltyProgramDetails() instanceof DigestInterface
                    ? $this->getLoyaltyProgramDetails()->getDigest()
                    : null,
            ]
        );
    }
}
