<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface AdditionalInfoResponseInterface
 *
 * @api
 */
interface AdditionalInfoResponseInterface
{
    /**
     * @return string|null
     */
    public function getWalletDetails(): ?string;

    /**
     * @return ContactInterface|null
     */
    public function getContact(): ?ContactInterface;

    /**
     * @return AddressDetailsInterface|null
     */
    public function getBillingDetails(): ?AddressDetailsInterface;

    /**
     * @return AddressDetailsInterface|null
     */
    public function getShippingDetails(): ?AddressDetailsInterface;

    /**
     * @return CardDetailInterface[];
     */
    public function getCardDetails(): array;

    /**
     * @return LoyaltyProgramDetailsInterface|null
     */
    public function getLoyaltyProgramDetails(): ?LoyaltyProgramDetailsInterface;
}
