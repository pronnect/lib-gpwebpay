<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface MpsPreCheckoutResponseInterface
 *
 * @api
 */
interface MpsPreCheckoutResponseInterface extends ResponseInterface
{
    /** SOAP-deserialized MpsPreCheckoutData object. */
    public function getPreCheckoutData(): mixed;

    public function getWalletPartnerLogoUrl(): ?string;

    public function getMasterpassLogoUrl(): ?string;
}
