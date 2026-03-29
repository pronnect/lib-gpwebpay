<?php

namespace Pronnect\GpWebPayTest\Unit;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Config;
use RuntimeException;

/**
 * Class ConfigTest
 * @covers \Pronnect\GpWebPay\Config
 */
class ConfigTest extends TestCase
{
    private array $configData;

    protected function setUp(): void
    {
        $this->configData = [
            'isTestEnvironment' => true,
            'provider' => 'TestProvider',
            'merchantNumber' => '123456',
            'merchantPrivateKey' => 'path/to/private_key.pem',
            'merchantPrivateKeyPassword' => getenv('GPWEBPAY_MERCHANT_PRIVATE_KEY_PASSWORD') ?: null,
            'GPEPublicKey' => 'path/to/public_key.pem',
            'wsClientOptions' => ['option1' => 'value1']
        ];
    }

    public function testGetProvider(): void
    {
        $config = new Config($this->configData);
        $this->assertSame('TestProvider', $config->getProvider());
    }

    public function testGetWsUri(): void
    {
        $config = new Config($this->configData);
        $this->assertSame('https://test.3dsecure.gpwebpay.com/pay-ws/v1/PaymentService', $config->getWsUri());
    }

    public function testGetWsClientOptions(): void
    {
        $config = new Config($this->configData);
        $this->assertSame(['option1' => 'value1'], $config->getWsClientOptions());
    }

    public function testGetGPEPublicKey(): void
    {
        $config = new Config($this->configData);
        $this->assertSame('path/to/public_key.pem', $config->getGPEPublicKey());
    }

    public function testIsTestEnvironment(): void
    {
        $config = new Config($this->configData);
        $this->assertTrue($config->isTestEnvironment());
    }

    public function testGetMerchantNumber(): void
    {
        $config = new Config($this->configData);
        $this->assertSame('123456', $config->getMerchantNumber());
    }

    public function testGetMerchantPrivateKey(): void
    {
        $config = new Config($this->configData);
        $this->assertSame('path/to/private_key.pem', $config->getMerchantPrivateKey());
    }

    public function testGetMerchantPrivateKeyPassword(): void
    {
        $config = new Config($this->configData);
        $this->assertSame(getenv('GPWEBPAY_MERCHANT_PRIVATE_KEY_PASSWORD') ?: null, $config->getMerchantPrivateKeyPassword());
    }

    public function testGetMerchantPrivateKeyPath(): void
    {
        $privKey = getenv('GPWEBPAY_MERCHANT_PRIVATE_KEY');
        $config = $this->configData;
        unset($config['merchantPrivateKey']);
        $config['merchantPrivateKeyPath'] = $privKey;
        $config = new Config($config);
        $this->assertSame(file_get_contents($privKey), $config->getMerchantPrivateKey());
    }

    public function testGetMerchantPrivateKeyPathWrong(): void
    {
        $this->expectException(RuntimeException::class);
        $config = $this->configData;
        unset($config['merchantPrivateKey']);
        $config['merchantPrivateKeyPath'] = '/nonexistent/path/key.pem';
        $config = new Config($config);
        $config->getMerchantPrivateKey();
    }

    public function testGetGPEPublicKeyPath(): void
    {
        $publicKey = getenv('GPWEBPAY_PUBLIC_KEY');
        $config = $this->configData;
        unset($config['GPEPublicKey']);
        $config['GPEPublicKeyPath'] = $publicKey;
        $config = new Config($config);
        $this->assertSame(file_get_contents($publicKey), $config->getGPEPublicKey());
    }

    public function testGetGPEPublicKeyWrong(): void
    {
        $this->expectException(RuntimeException::class);
        $config = $this->configData;
        unset($config['GPEPublicKey']);
        $config['GPEPublicKeyPath'] = '/nonexistent/path/key.pem';
        $config = new Config($config);
        $config->getGPEPublicKey();
    }

    public function testGetGPEPublicKeyDefault(): void
    {
        $config = $this->configData;
        unset($config['GPEPublicKey']);
        $config = new Config($config);
        $this->assertNotEmpty($config->getGPEPublicKey());
    }

}
