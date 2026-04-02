<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Http;

use Pronnect\GpWebPay\Config;
use Pronnect\GpWebPayApi\Http\HttpConfigInterface;

/**
 * Configuration for the GP Webpay HTTP API client.
 *
 * Wraps the existing Config (WS) and adds HTTP-specific settings.
 *
 * Usage:
 *   $config = new HttpConfig([
 *       'isTestEnvironment'          => true,
 *       'merchantNumber'             => '0123456789',
 *       'merchantPrivateKeyPath'     => '/certs/merchant.pem',
 *       'merchantPrivateKeyPassword' => 'heslo',
 *       'GPEPublicKeyPath'           => '/certs/gpe.signing_test.pem',
 *       'defaultLang'                => 'CS',
 *   ]);
 */
class HttpConfig extends Config implements HttpConfigInterface
{
    protected const HTTP_URI_TEST = 'https://test.3dsecure.gpwebpay.com/pgw/order.do';
    protected const HTTP_URI_PROD = 'https://3dsecure.gpwebpay.com/pgw/order.do';

    private ?string $defaultLang;

    public function __construct(array $configData)
    {
        parent::__construct($configData);

        $lang             = (string) ($configData['defaultLang'] ?? '') ?: null;
        $this->defaultLang = $lang !== null ? strtoupper($lang) : null;
    }

    public function getHttpUri(): string
    {
        return $this->isTestEnvironment()
            ? self::HTTP_URI_TEST
            : self::HTTP_URI_PROD;
    }

    public function getDefaultLang(): ?string
    {
        return $this->defaultLang;
    }
}
