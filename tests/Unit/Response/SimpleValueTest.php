<?php

namespace Pronnect\GpWebPayTest\Unit\Response;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Response\SimpleValue;
use Pronnect\GpWebPayApi\DigestInterface;
use ReflectionClass;

/**
 * @covers \Pronnect\GpWebPay\Response\SimpleValue
 */
class SimpleValueTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetName(): void
    {
        $simpleValue = new SimpleValue();
        $simpleValueReflection = new ReflectionClass($simpleValue);
        $nameProperty = $simpleValueReflection->getProperty('name');
        $nameProperty->setAccessible(true);
        $nameProperty->setValue($simpleValue, 'TestName');

        $this->assertSame('TestName', $simpleValue->getName());
    }

    /**
     * @return void
     */
    public function testGetValue(): void
    {
        $simpleValue = new SimpleValue();
        $simpleValueReflection = new ReflectionClass($simpleValue);
        $valueProperty = $simpleValueReflection->getProperty('value');
        $valueProperty->setAccessible(true);
        $valueProperty->setValue($simpleValue, 'TestValue');

        $this->assertSame('TestValue', $simpleValue->getValue());
    }

    /**
     * @return void
     */
    public function testGetDigest(): void
    {
        $simpleValue = new SimpleValue();
        $simpleValueReflection = new ReflectionClass($simpleValue);
        $nameProperty = $simpleValueReflection->getProperty('name');
        $nameProperty->setAccessible(true);
        $nameProperty->setValue($simpleValue, 'TestName');
        $valueProperty = $simpleValueReflection->getProperty('value');
        $valueProperty->setAccessible(true);
        $valueProperty->setValue($simpleValue, 'TestValue');

        $expectedDigest = implode(DigestInterface::DIGEST_SEPARATOR, [
            'TestName',
            'TestValue',
        ]);
        $this->assertSame($expectedDigest, $simpleValue->getDigest());
    }
}
