<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Http;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Http\HttpConfig;

/**
 * @covers \Pronnect\GpWebPay\Http\HttpConfig
 * @uses   \Pronnect\GpWebPay\Config
 */
class HttpConfigTest extends TestCase
{
    public function testGetHttpUriReturnsTestEndpointWhenIsTestEnvironment(): void
    {
        $config = new HttpConfig([
            'isTestEnvironment' => true,
            'merchantNumber'    => '0123456789',
        ]);

        $this->assertSame(
            'https://test.3dsecure.gpwebpay.com/pgw/order.do',
            $config->getHttpUri(),
        );
    }

    public function testGetHttpUriReturnsProdEndpointWhenNotTestEnvironment(): void
    {
        $config = new HttpConfig([
            'isTestEnvironment' => false,
            'merchantNumber'    => '0123456789',
        ]);

        $this->assertSame(
            'https://3dsecure.gpwebpay.com/pgw/order.do',
            $config->getHttpUri(),
        );
    }

    public function testGetDefaultLangReturnsNullWhenNotSet(): void
    {
        $config = new HttpConfig([
            'isTestEnvironment' => true,
            'merchantNumber'    => '0123456789',
        ]);

        $this->assertNull($config->getDefaultLang());
    }

    public function testGetDefaultLangReturnsUppercaseValue(): void
    {
        $config = new HttpConfig([
            'isTestEnvironment' => true,
            'merchantNumber'    => '0123456789',
            'defaultLang'       => 'cs',
        ]);

        $this->assertSame('CS', $config->getDefaultLang());
    }

    public function testGetDefaultLangReturnsNullForEmptyString(): void
    {
        $config = new HttpConfig([
            'isTestEnvironment' => true,
            'merchantNumber'    => '0123456789',
            'defaultLang'       => '',
        ]);

        $this->assertNull($config->getDefaultLang());
    }
}