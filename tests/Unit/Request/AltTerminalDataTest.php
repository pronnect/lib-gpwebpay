<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Request;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Request\AltTerminalData;
use Pronnect\GpWebPayApi\DigestInterface;

/**
 * @covers \Pronnect\GpWebPay\Request\AltTerminalData
 */
class AltTerminalDataTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $data = new AltTerminalData();
        $data->setTerminalId('TERM01')
            ->setTerminalOwner('Merchant Name')
            ->setTerminalCity('Bratislava');

        $this->assertSame('TERM01', $data->getTerminalId());
        $this->assertSame('Merchant Name', $data->getTerminalOwner());
        $this->assertSame('Bratislava', $data->getTerminalCity());
    }

    public function testSettersReturnSelf(): void
    {
        $data = new AltTerminalData();
        $this->assertSame($data, $data->setTerminalId('T'));
        $this->assertSame($data, $data->setTerminalOwner('Owner'));
        $this->assertSame($data, $data->setTerminalCity('City'));
    }

    public function testDefaultsAreNull(): void
    {
        $data = new AltTerminalData();
        $this->assertNull($data->getTerminalId());
        $this->assertNull($data->getTerminalOwner());
        $this->assertNull($data->getTerminalCity());
    }

    public function testGetDigestWithAllFields(): void
    {
        $data = new AltTerminalData();
        $data->setTerminalId('TERM01')
            ->setTerminalOwner('Merchant')
            ->setTerminalCity('Prague');

        $expected = implode(DigestInterface::DIGEST_SEPARATOR, ['TERM01', 'Merchant', 'Prague']);
        $this->assertSame($expected, $data->getDigest());
    }

    public function testGetDigestWithPartialFields(): void
    {
        $data = new AltTerminalData();
        $data->setTerminalId('TERM01');

        $this->assertSame('TERM01', $data->getDigest());
    }

    public function testGetDigestReturnsNullWhenEmpty(): void
    {
        $this->assertNull((new AltTerminalData())->getDigest());
    }
}
