<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Request\RequestInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class BatchCloseRequest
 *
 * Uses merchantKey (provider + merchantNumber), no paymentNumber.
 * WSDL input element name is "batchClose" — Gateway::WSDL_REQUEST_NAME_OVERRIDES handles this.
 */
class BatchCloseRequest implements RequestInterface, SignedInterface
{
    use RequestTrait;
    use DigestTrait;
    use SignedTrait;

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->messageId ?? null,
            $this->provider ?? null,
            $this->merchantNumber ?? null,
        ]);
    }
}
