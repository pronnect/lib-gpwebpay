<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Http;

use Pronnect\GpWebPayApi\Http\Request\HttpRequestInterface;
use Pronnect\GpWebPayApi\Http\Response\HttpResponseInterface;

/**
 * Main entry point for the GP Webpay HTTP API.
 *
 * @api
 */
interface HttpGatewayInterface
{
    /**
     * Returns a GET redirect URL for the customer.
     *
     * LANG is appended AFTER signing (not part of the digest).
     *
     * ⚠️ Throws HttpRequestException if ADDINFO is set on the request.
     * ADDINFO can exceed URL limits and browsers encode whitespace inconsistently,
     * which corrupts the signature. Use getFormParams() + POST for ADDINFO requests.
     *
     * @throws HttpRequestException if the request contains ADDINFO
     */
    public function getRedirectUrl(HttpRequestInterface $request): string;

    /**
     * Returns signed parameters for a POST form submission.
     *
     * Caller is responsible for rendering the HTML form:
     *   <form method="POST" action="<?= $gateway->getHttpUri() ?>">
     *     <?php foreach ($params as $name => $value): ?>
     *       <input type="hidden" name="<?= htmlspecialchars($name) ?>"
     *              value="<?= htmlspecialchars($value) ?>">
     *     <?php endforeach; ?>
     *   </form>
     *
     * Use this method always when ADDINFO is present.
     *
     * @return array<string, string>
     */
    public function getFormParams(HttpRequestInterface $request): array;

    /**
     * Verifies DIGEST + DIGEST1 signatures and parses the callback from GP Webpay.
     *
     * Call with raw $_GET or $_POST after the customer is redirected back.
     *
     * @param array<string, string|null> $params  Raw $_GET or $_POST callback parameters
     *
     * @throws InvalidCallbackException if required DIGEST parameter is missing
     * @throws InvalidDigestException   if DIGEST or DIGEST1 verification fails
     */
    public function processCallback(array $params): HttpResponseInterface;

    /**
     * GP Webpay HTTP API endpoint URL (use as POST form action or base for redirect URL).
     */
    public function getHttpUri(): string;
}
