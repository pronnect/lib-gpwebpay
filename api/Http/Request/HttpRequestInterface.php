<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Http\Request;

/**
 * Base interface for GP Webpay HTTP API payment requests.
 *
 * @api
 */
interface HttpRequestInterface
{
    /**
     * Returns the ADDINFO XML string, or null if not set.
     * Used by HttpGateway to enforce POST-only when ADDINFO is present.
     */
    public function getAddInfo(): ?string;

    /**
     * Returns the LANG value, or null to fall back to HttpConfig::getDefaultLang().
     * LANG is NOT included in the digest — added to params after signing.
     */
    public function getLang(): ?string;

    /**
     * Returns all non-null, non-empty request fields as an array (without LANG and DIGEST).
     * Used by HttpGateway to build the signed parameter array.
     *
     * @return array<string, string|int>
     */
    public function toArray(): array;
}
