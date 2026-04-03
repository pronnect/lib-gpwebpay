<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface CardDataResponseInterface
 *
 * @api
 */
interface CardDataResponseInterface extends ResponseInterface
{
    /**
     * @return string|null
     */
    public function getContentType(): ?string;

    /**
     * @return int|null
     */
    public function getWidth(): ?int;

    /**
     * @return int|null
     */
    public function getHeight(): ?int;

    /**
     * @return string|null Base64Binary image data
     */
    public function getData(): ?string;

    /**
     * @return string|null
     */
    public function getPanMasked(): ?string;

    /**
     * @return int|null
     */
    public function getExpiryMonth(): ?int;

    /**
     * @return int|null
     */
    public function getExpiryYear(): ?int;

    /**
     * @return string|null
     */
    public function getAssociation(): ?string;

    /**
     * @return string|null
     */
    public function getErrorDescription(): ?string;
}
