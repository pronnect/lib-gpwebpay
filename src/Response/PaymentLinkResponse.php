<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPay\SignedTrait;
use Pronnect\GpWebPayApi\Response\MessageInterface;
use Pronnect\GpWebPayApi\Response\PaymentLinkResponseInterface;
use Pronnect\GpWebPayApi\Response\PaymentResponseInterface;
use Pronnect\GpWebPayApi\SignedInterface;

/**
 * Class PaymentLinkResponse
 */
class PaymentLinkResponse
    extends Response
    implements PaymentLinkResponseInterface, PaymentResponseInterface, MessageInterface, SignedInterface
{
    use MessageTrait;
    use PaymentResponseTrait;
    use DigestTrait;
    use SignedTrait;

    protected ?string $paymentLink = null;

    /**
     * @return string|null
     */
    public function getPaymentLink(): ?string
    {
        return $this->paymentLink;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest(
            [
                $this->getMessageId(),
                $this->getPaymentNumber(),
                $this->paymentLink ?? null,
            ]
        );
    }
}
