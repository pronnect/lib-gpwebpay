<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay;

use Exception;
use InvalidArgumentException;
use Pronnect\GpWebPayApi\DigestSignerInterface;
use Pronnect\GpWebPayApi\GatewayInterface;
use Pronnect\GpWebPayApi\Request\RequestInterface;
use Pronnect\GpWebPayApi\Response\ResponseInterface;
use Pronnect\GpWebPayApi\SignedInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;
use RuntimeException;
use SoapFault;

/**
 * Class Gateway
 *
 * @method Response\StateResponse                 getPaymentStatus(Request\PaymentStatusRequest $request)
 * @method Response\PaymentDetailResponse         getPaymentDetail(Request\PaymentDetailRequest $request)
 * @method Response\StatusResponse                getPaymentLinkStatus(Request\PaymentLinkStatusRequest $request)
 * @method Response\PaymentLinkResponse           createPaymentLink(Request\PaymentLinkRequest $request)
 * @method Response\StateResponse                 revokePaymentLink(Request\RevokePaymentLinkRequest $request)
 * @method Response\StatusResponse                getTokenStatus(Request\TokenStatusRequest $request)
 * @method Response\StatusResponse                processTokenRevoke(Request\TokenRevokeRequest $request)
 * @method Response\CardOnFilePaymentResponse     processCardOnFilePayment(Request\CardOnFilePaymentRequest $request)
 * @method Response\CardDataResponse              getCardData(Request\CardDataRequest $request)
 * @method Response\StateResponse                 processCapture(Request\CaptureRequest $request)
 * @method Response\StateResponse                 processCaptureReverse(Request\CaptureReverseRequest $request)
 * @method Response\StateResponse                 processAuthorizationReverse(Request\AuthorizationReverseRequest $request)
 * @method Response\StateResponse                 processPaymentClose(Request\PaymentCloseRequest $request)
 * @method Response\StateResponse                 processPaymentDelete(Request\PaymentDeleteRequest $request)
 * @method Response\StateResponse                 processRefund(Request\RefundRequest $request)
 * @method Response\StateResponse                 processRefundReverse(Request\RefundReverseRequest $request)
 * @method Response\StatusResponse                getMasterPaymentStatus(Request\MasterPaymentStatusRequest $request)
 * @method Response\StatusResponse                processMasterPaymentRevoke(Request\MasterPaymentStatusRequest $request)
 * @method Response\RecurringPaymentResponse      processRecurringPayment(Request\RecurringPaymentRequest $request)
 * @method Response\RecurringPaymentResponse      processUsageBasedPayment(Request\UsageBasedPaymentRequest $request)
 * @method Response\RecurringPaymentResponse      processUsageBasedSubscriptionPayment(Request\UsageBasedSubscriptionPaymentRequest $request)
 * @method Response\RecurringPaymentResponse      processRegularSubscriptionPayment(Request\RegularSubscriptionPaymentRequest $request)
 * @method Response\RecurringPaymentResponse      processPrepaidPayment(Request\PrepaidPaymentRequest $request)
 * @method Response\BatchCloseResponse            processBatchClose(Request\BatchCloseRequest $request)
 * @method Response\RecurringPaymentResponse      processPayout(Request\PayoutRequest $request)
 * @method Response\RecurringPaymentResponse      processPayoutWinning(Request\PayoutWinningRequest $request)
 * @method Response\RecurringPaymentResponse      processPayoutInsurance(Request\PayoutInsuranceRequest $request)
 * @method Response\SubsqTransBatchStatusResponse getSubsqTransBatchStatus(Request\SubsqTransBatchStatusRequest $request)
 * @method Response\StateResponse                 resolvePaymentStatus(Request\ResolvePaymentStatusRequest $request)
 * @method Response\MpsPreCheckoutResponse        mpsPreCheckout(Request\MpsPreCheckoutRequest $request)
 * @method Response\RecurringPaymentResponse      mpsExpressCheckout(Request\MpsExpressCheckoutRequest $request)
 */
class Gateway implements GatewayInterface
{
    /**
     * WSDL anomalies where the SOAP input element name does not follow
     * the standard lcfirst(ClassName) convention.
     */
    private const REQUEST_NAME_OVERRIDES = [
        'batchCloseRequest' => 'batchClose',
    ];

    /**
     * WSDL anomalies where the response wrapper element name does not follow
     * the standard "{requestName} → {baseName}Response" convention.
     */
    private const RESPONSE_NAME_OVERRIDES = [
        'refundRequest'                  => 'refundRequestResponse',
        'resolvePaymentStatusRequest'    => 'paymentStatusResponse',
    ];

    private Config $config;
    private WebService $soapClient;
    private ?DigestSignerInterface $digestSigner;
    private ?LoggerInterface $logger;

    /**
     * Constructor Gateway
     *
     * @param Config $config
     * @param DigestSignerInterface|null $digestSigner
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Config $config,
        ?DigestSignerInterface $digestSigner = null,
        ?LoggerInterface $logger = null
    ) {
        $this->config = $config;
        $this->digestSigner = $digestSigner;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @return void
     * @throws SoapFault
     */
    public function echo(): void
    {
        $this->getSoapClient()->echo(null);
    }

    /**
     * @return WebService
     * @throws SoapFault
     */
    protected function getSoapClient(): WebService
    {
        if (!isset($this->soapClient)) {
            $this->soapClient = new WebService(
                null,
                array_merge_recursive(
                    $this->config->getWsClientOptions(),
                    [
                        'location' => $this->config->getWsUri(),
                        'logger'   => $this->logger,
                    ],
                )
            );
        }

        return $this->soapClient;
    }

    /**
     * @param string $method
     * @param array $params
     *
     * @return ResponseInterface
     * @throws SoapFault
     * @throws Exception
     */
    public function __call(string $method, array $params): ResponseInterface
    {
        $request = $params[0] ?? null;
        if (!$request instanceof RequestInterface) {
            throw new InvalidArgumentException("First parameter must be instance of RequestInterface");
        }
        if (!$request->getProvider()) {
            $request->setProvider($this->config->getProvider());
        }
        if (!$request->getMerchantNumber()) {
            $request->setMerchantNumber($this->config->getMerchantNumber());
        }
        if (!$request->getMessageId()) {
            $request->setMessageId(bin2hex(random_bytes(16)));
        }
        if ($request instanceof SignedInterface) {
            $this->logger->debug(sprintf('Digest for sign "%s"', $request->getDigest()));
            $request->setSignature($this->getDigestSigner()->sign((string)$request->getDigest()));
        }

        return $this->callWS($method, $request);
    }

    public function getLastRequest(): ?string
    {
        return isset($this->soapClient) ? $this->soapClient->__getLastRequest() : null;
    }

    public function getLastResponse(): ?string
    {
        return isset($this->soapClient) ? $this->soapClient->__getLastResponse() : null;
    }

    /**
     * @return DigestSignerInterface
     */
    protected function getDigestSigner(): DigestSignerInterface
    {
        if (!isset($this->digestSigner)) {
            $this->digestSigner = new DigestSigner(
                $this->config->getGPEPublicKey(),
                $this->config->getMerchantPrivateKey(),
                $this->config->getMerchantPrivateKeyPassword()
            );
        }

        return $this->digestSigner;
    }

    /**
     * @param string $method
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     * @throws SoapFault
     */
    protected function callWS(
        string $method,
        RequestInterface $request
    ): ResponseInterface {
        $reflect = new ReflectionClass($request);
        $requestName = lcfirst($reflect->getShortName());
        $requestKey = self::REQUEST_NAME_OVERRIDES[$requestName] ?? $requestName;
        $responseName = self::RESPONSE_NAME_OVERRIDES[$requestName]
            ?? preg_replace('~Request$~', 'Response', $requestName, 1);
        try {
            $rawResponse = $this->getSoapClient()->$method([$requestKey => $request]);
            $response = $rawResponse->$responseName;
        } catch (SoapFault $soapFault) {
            if (isset($soapFault->detail->serviceException)) {
                $serviceException = new ServiceException();
                $serviceException->setMessageId($soapFault->detail->serviceException->messageId)
                    ->setPrimaryReturnCode(
                        (string)($soapFault->detail->serviceException->primaryReturnCode ?? null)
                    )
                    ->setSecondaryReturnCode(
                        (string)($soapFault->detail->serviceException->secondaryReturnCode ?? null)
                    )
                    ->setSignature((string)$soapFault->detail->serviceException->signature ?: null);
                $validException = $this->getDigestSigner()->verify(
                    (string)$serviceException->getDigest(),
                    base64_decode((string)$serviceException->getSignature())
                );
                if (!$validException) {
                    throw new RuntimeException("Response signature is not valid", 0, $soapFault);
                }
                $soapFault->detail->serviceException = $serviceException;
            }
            throw $soapFault;
        }

        if ($response instanceof SignedInterface) {
            $valid = $this->getDigestSigner()->verify(
                (string)$response->getDigest(), (string)$response->getSignature()
            );
            if (!$valid) {
                throw new RuntimeException("Response signature is not valid");
            }
        }

        return $response;
    }
}
