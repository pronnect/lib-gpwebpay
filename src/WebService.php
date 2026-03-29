<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay;

use Exception;
use Pronnect\GpWebPay\Request\AltTerminalData;
use Pronnect\GpWebPay\Request\BillingDetails;
use Pronnect\GpWebPay\Request\CardHolderData;
use Pronnect\GpWebPay\Request\CardholderDetails;
use Pronnect\GpWebPay\Request\PaymentInfo;
use Pronnect\GpWebPay\Request\ShippingDetails;
use Pronnect\GpWebPay\Request\ShoppingCartInfo;
use Pronnect\GpWebPay\Request\ShoppingCartItem;
use Pronnect\GpWebPay\Request\SubMerchantData;
use Pronnect\GpWebPay\Response\AdditionalInfoResponse;
use Pronnect\GpWebPay\Response\AddressDetails;
use Pronnect\GpWebPay\Response\BatchCloseResponse;
use Pronnect\GpWebPay\Response\CardDataResponse;
use Pronnect\GpWebPay\Response\CardDetail;
use Pronnect\GpWebPay\Response\CardOnFilePaymentFaultDetail;
use Pronnect\GpWebPay\Response\CardOnFilePaymentResponse;
use Pronnect\GpWebPay\Response\Contact;
use Pronnect\GpWebPay\Response\LoyaltyProgramDetails;
use Pronnect\GpWebPay\Response\MpsPreCheckoutResponse;
use Pronnect\GpWebPay\Response\PaymentDetailResponse;
use Pronnect\GpWebPay\Response\PaymentLinkResponse;
use Pronnect\GpWebPay\Response\RecurringPaymentResponse;
use Pronnect\GpWebPay\Response\SimpleValue;
use Pronnect\GpWebPay\Response\StateResponse;
use Pronnect\GpWebPay\Response\StatusResponse;
use Pronnect\GpWebPay\Response\SubsqTransBatchStatusResponse;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SoapClient;
use SoapFault;

/**
 * Class WebService
 */
class WebService extends SoapClient
{
    private LoggerInterface $logger;
    private bool $traceEnabled = false;

    /**
     * Constructor WebService
     *
     * @param       $wsdl
     * @param array $options
     *
     * @throws SoapFault
     */
    public function __construct($wsdl, array $options = [])
    {
        $this->logger      = $options['logger'] ?? new NullLogger();
        $this->traceEnabled = (bool)($options['trace'] ?? false);
        unset($options['logger']);
        try {
            $wsdl = $wsdl ?: dirname(__DIR__) . '/resources/wsdl/cws_v1.wsdl';
            $options['classmap'] = array_merge_recursive([
                "ServiceException"          => ServiceException::class,
                "PaymentStatusResponse"     => StateResponse::class,
                "PaymentDetailResponse"     => PaymentDetailResponse::class,
                "AdditionalInfoResponse"    => AdditionalInfoResponse::class,
                "CardDetail"                => CardDetail::class,
                "Contact"                   => Contact::class,
                "LoyaltyProgramDetails"     => LoyaltyProgramDetails::class,
                "AddressDetails"            => AddressDetails::class,
                "SimpleValue"               => SimpleValue::class,
                "PaymentLinkResponse"       => PaymentLinkResponse::class,
                "PaymentLinkStatusResponse"      => StateResponse::class,
                "RevokePaymentLinkResponse"      => StateResponse::class,
                "TokenStatusResponse"            => StatusResponse::class,
                "TokenRevokeResponse"            => StatusResponse::class,
                "CardOnFilePaymentResponse"      => CardOnFilePaymentResponse::class,
                "CardOnFilePaymentFaultDetail"   => CardOnFilePaymentFaultDetail::class,
                "CardDataResponse"               => CardDataResponse::class,
                "CaptureResponse"               => StateResponse::class,
                "CaptureReverseResponse"        => StateResponse::class,
                "AuthorizationReverseResponse"  => StateResponse::class,
                "PaymentCloseResponse"          => StateResponse::class,
                "PaymentDeleteResponse"         => StateResponse::class,
                "RefundResponse"                => StateResponse::class,
                "RefundReverseResponse"         => StateResponse::class,
                "MasterPaymentStatusResponse"            => StatusResponse::class,
                "RecurringPaymentResponse"               => RecurringPaymentResponse::class,
                "UsageBasedPaymentResponse"              => CardOnFilePaymentResponse::class,
                "UsageBasedSubscriptionPaymentResponse"  => RecurringPaymentResponse::class,
                "RegularSubscriptionPaymentResponse"     => RecurringPaymentResponse::class,
                "PrepaidPaymentResponse"                 => RecurringPaymentResponse::class,
                "BatchCloseResponse"                     => BatchCloseResponse::class,
                "PayoutWinningResponse"                  => RecurringPaymentResponse::class,
                "PayoutInsuranceResponse"                => RecurringPaymentResponse::class,
                "PayoutResponse"                         => RecurringPaymentResponse::class,
                "SubsqTransBatchStatusResponse"          => SubsqTransBatchStatusResponse::class,
                "MpsPreCheckoutResponse"                 => MpsPreCheckoutResponse::class,
                "MpsExpressCheckoutResponse"             => RecurringPaymentResponse::class,
                "SubMerchantData"                        => SubMerchantData::class,
                "AltTerminalData"                        => AltTerminalData::class,
                "PaymentInfo"                            => PaymentInfo::class,
                "ShoppingCartInfo"                       => ShoppingCartInfo::class,
                "ShoppingCartItem"                       => ShoppingCartItem::class,
                "CardHolderData"                         => CardHolderData::class,
                "CardholderDetails"                      => CardholderDetails::class,
                "BillingDetails"                         => BillingDetails::class,
                "ShippingDetails"                        => ShippingDetails::class,
            ],
                $options['classmap'] ?? []
            );
            parent::__construct($wsdl, $options);
        } catch (SoapFault $soapFault) {
            $this->logger->critical($soapFault, ['exception' => $soapFault]);
            throw $soapFault;
        }
    }

    /**
     * @throws Exception
     */
    public function __doRequest(string $request, string $location, string $action, int $version, bool $oneWay = false): ?string
    {
        try {
            $startTime = microtime(true);
            $response = parent::__doRequest($request, $location, $action, $version, $oneWay);
        } catch (Exception $exc) {
            $this->logger->critical($exc, ['exception' => $exc]);
            throw $exc;
        } finally {
            $this->logger->info(
                sprintf(
                    __METHOD__ . " call time %.4f s", microtime(true) - $startTime
                ),
                [
                    'location' => $location,
                    'action'   => $action,
                    'version'  => $version,
                    'oneWay'   => $oneWay,
                ]
            );
            if ($this->traceEnabled) {
                $this->logger->debug($this->__getLastRequestHeaders(), ['type' => 'LAST_REQUEST_HEADER']);
                $this->logger->debug($this->__getLastRequest(), ['type' => 'LAST_REQUEST']);
                $this->logger->debug($this->__getLastResponseHeaders(), ['type' => 'LAST_RESPONSE_HEADERS']);
                $this->logger->debug($response ?? null, ['type' => 'LAST_RESPONSE']);
            }
        }

        return $response;
    }
}
