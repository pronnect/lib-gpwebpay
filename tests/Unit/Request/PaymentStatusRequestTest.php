<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\PaymentStatusRequest;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * Class PaymentStatusRequestTest
 * @covers \Pronnect\GpWebPay\Request\PaymentStatusRequest
 */
class PaymentStatusRequestTest extends TestCase
{
    public function testSetAndGetData(): void
    {
        $request = new PaymentStatusRequest();

        $request->setMessageId('12345');
        $this->assertSame('12345', $request->getMessageId());

        $request->setProvider('provider');
        $this->assertSame('provider', $request->getProvider());

        $request->setMerchantNumber('67890');
        $this->assertSame('67890', $request->getMerchantNumber());

        $request->setPaymentNumber('54321');
        $this->assertSame('54321', $request->getPaymentNumber());
    }

    public function testGetDigest(): void
    {
        $request = new PaymentStatusRequest();
        $requestReflection = new ReflectionClass($request);

        $properties = [
            'messageId' => '12345',
            'provider' => 'provider',
            'merchantNumber' => '67890',
            'paymentNumber' => '54321',
        ];

        foreach ($properties as $property => $value) {
            $propertyReflection = $requestReflection->getProperty($property);
            $propertyReflection->setAccessible(true);
            $propertyReflection->setValue($request, $value);
        }

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            '12345',
            'provider',
            '67890',
            '54321'
        ]);

        $this->assertSame($expectedDigest, $request->getDigest());
    }
}
