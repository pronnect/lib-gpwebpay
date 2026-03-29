<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi;

use Pronnect\GpWebPayApi\Request\RequestInterface;
use Pronnect\GpWebPayApi\Response\BatchCloseResponseInterface;
use Pronnect\GpWebPayApi\Response\CardDataResponseInterface;
use Pronnect\GpWebPayApi\Response\CardOnFilePaymentResponseInterface;
use Pronnect\GpWebPayApi\Response\MpsPreCheckoutResponseInterface;
use Pronnect\GpWebPayApi\Response\PaymentDetailResponseInterface;
use Pronnect\GpWebPayApi\Response\PaymentLinkResponseInterface;
use Pronnect\GpWebPayApi\Response\RecurringPaymentResponseInterface;
use Pronnect\GpWebPayApi\Response\ResponseInterface;
use Pronnect\GpWebPayApi\Response\StateResponseInterface;
use Pronnect\GpWebPayApi\Response\StatusResponseInterface;
use Pronnect\GpWebPayApi\Response\SubsqTransBatchStatusResponseInterface;

/**
 * Interface GatewayInterface
 *
 * @api
 */
interface GatewayInterface
{
    /** Connectivity check — no request/response body. */
    public function echo(): void;
}
