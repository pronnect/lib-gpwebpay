<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http\Response;

use Pronnect\GpWebPayApi\Http\Response\HttpResponseInterface;

/**
 * Parsed GP Webpay HTTP API callback response.
 *
 * Usage (raw):
 *   $response = HttpResponse::fromArray($_POST);
 *
 * Usage (PSR-7 duck-typing):
 *   $response = HttpResponse::fromServerRequest($symfonyRequest);
 *
 * ⚠️ Always verify DIGEST/DIGEST1 via HttpGateway::processCallback() before constructing
 * this object from untrusted input. The gateway calls HttpResponse::fromArray() internally.
 */
class HttpResponse implements HttpResponseInterface
{
    private function __construct(
        private string  $operation,
        private int     $orderNumber,
        private ?string $merOrderNum,
        private ?string $md,
        private int     $prCode,
        private int     $srCode,
        private ?string $resultText,
        private ?string $token,
        private ?string $expiry,
        private ?string $acsRes,
        private ?string $acCode,
        private ?string $panPattern,
        private ?string $dayToCapture,
        private ?string $tokenRegStatus,
        private ?string $acrc,
        private ?string $rrn,
        private ?string $par,
        private ?string $traceId,
    ) {}

    /**
     * Primary factory — builds from raw callback array ($_GET or $_POST).
     *
     * @param array<string, string|null> $params
     */
    public static function fromArray(array $params): static
    {
        return new static(
            operation:      (string) ($params['OPERATION']       ?? ''),
            orderNumber:    (int)    ($params['ORDERNUMBER']     ?? 0),
            merOrderNum:    ($params['MERORDERNUM']    ?? null) ?: null,
            md:             ($params['MD']             ?? null) ?: null,
            prCode:         (int)    ($params['PRCODE']          ?? 0),
            srCode:         (int)    ($params['SRCODE']          ?? 0),
            resultText:     ($params['RESULTTEXT']     ?? null) ?: null,
            token:          ($params['TOKEN']          ?? null) ?: null,
            expiry:         ($params['EXPIRY']         ?? null) ?: null,
            acsRes:         ($params['ACSRES']         ?? null) ?: null,
            acCode:         ($params['ACCODE']         ?? null) ?: null,
            panPattern:     ($params['PANPATTERN']     ?? null) ?: null,
            dayToCapture:   ($params['DAYTOCAPTURE']   ?? null) ?: null,
            tokenRegStatus: ($params['TOKENREGSTATUS'] ?? null) ?: null,
            acrc:           ($params['ACRC']           ?? null) ?: null,
            rrn:            ($params['RRN']            ?? null) ?: null,
            par:            ($params['PAR']            ?? null) ?: null,
            traceId:        ($params['TRACEID']        ?? null) ?: null,
        );
    }

    /**
     * PSR-7 factory — integrates with Symfony, Laravel, and other PSR-7 frameworks.
     *
     * Uses duck-typing so psr/http-message is NOT a hard Composer dependency.
     * If the consumer doesn't have PSR-7, this method is simply never called.
     *
     * Supports both GET and POST requests (determined by getMethod()).
     *
     * @param object $request  Object implementing PSR-7 ServerRequestInterface
     *                         (requires: getMethod(), getParsedBody(), getQueryParams())
     */
    public static function fromServerRequest(object $request): static
    {
        $method = strtolower($request->getMethod());
        $params = $method === 'post'
            ? (array) $request->getParsedBody()
            : $request->getQueryParams();

        return static::fromArray($params);
    }

    public function getOperation(): string      { return $this->operation; }
    public function getOrderNumber(): int       { return $this->orderNumber; }
    public function getMerOrderNum(): ?string   { return $this->merOrderNum; }
    public function getMd(): ?string            { return $this->md; }
    public function getPrCode(): int            { return $this->prCode; }
    public function getSrCode(): int            { return $this->srCode; }
    public function getResultText(): ?string    { return $this->resultText; }
    public function getToken(): ?string         { return $this->token; }
    public function getExpiry(): ?string        { return $this->expiry; }
    public function getAcsRes(): ?string        { return $this->acsRes; }
    public function getAcCode(): ?string        { return $this->acCode; }
    public function getPanPattern(): ?string    { return $this->panPattern; }
    public function getDayToCapture(): ?string  { return $this->dayToCapture; }
    public function getTokenRegStatus(): ?string { return $this->tokenRegStatus; }
    public function getAcrc(): ?string          { return $this->acrc; }
    public function getRrn(): ?string           { return $this->rrn; }
    public function getPar(): ?string           { return $this->par; }
    public function getTraceId(): ?string       { return $this->traceId; }

    public function isSuccess(): bool
    {
        return $this->prCode === 0 && $this->srCode === 0;
    }
}
