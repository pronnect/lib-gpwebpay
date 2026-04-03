<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface CardDetailInterface
 *
 * @api
 */
interface CardDetailInterface
{
    /**
     * @return string|null
     */
    public function getBrandId(): ?string;

    /**
     * @return string|null
     */
    public function getBrandName(): ?string;

    /**
     * @return string|null
     */
    public function getCardHolderName(): ?string;

    /**
     * @return string|null
     */
    public function getExpiryMonth(): ?string;

    /**
     * @return string|null
     */
    public function getExpiryYear(): ?string;

    /**
     * @return string|null
     */
    public function getCardId(): ?string;

    /**
     * @return string|null
     */
    public function getLastFour(): ?string;

    /**
     * @return string|null
     */
    public function getCardAlias(): ?string;
}
