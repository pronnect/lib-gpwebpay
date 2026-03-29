<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\PaymentLinkResponse;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Response\PaymentLinkResponse
 * @covers \Pronnect\GpWebPay\Response\Response::__set
 */
class PaymentLinkResponseTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetPaymentLink(): void
    {
        $response = new PaymentLinkResponse();
        $paymentLinkReflection = new \ReflectionClass($response);
        $paymentLinkProperty = $paymentLinkReflection->getProperty('paymentLink');
        $paymentLinkProperty->setAccessible(true);
        $paymentLinkProperty->setValue($response, 'https://example.com/payment');

        $this->assertSame('https://example.com/payment', $response->getPaymentLink());
    }

    /**
     * @return void
     */
    public function testGetDigest(): void
    {
        $response = new PaymentLinkResponse();
        $responseReflection = new \ReflectionClass($response);
        $messageIdProperty = $responseReflection->getProperty('messageId');
        $messageIdProperty->setAccessible(true);
        $messageIdProperty->setValue($response, '12345');
        $paymentNumberProperty = $responseReflection->getProperty('paymentNumber');
        $paymentNumberProperty->setAccessible(true);
        $paymentNumberProperty->setValue($response, '54321');
        $paymentLinkProperty = $responseReflection->getProperty('paymentLink');
        $paymentLinkProperty->setAccessible(true);
        $paymentLinkProperty->setValue($response, 'https://example.com/payment');

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            '12345',
            '54321',
            'https://example.com/payment',
        ]);
        $this->assertSame($expectedDigest, $response->getDigest());
    }
}
