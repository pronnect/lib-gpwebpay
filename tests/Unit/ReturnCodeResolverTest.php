<?php

declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\ReturnCodeResolver;

/**
 * @covers \Pronnect\GpWebPay\ReturnCodeResolver
 */
class ReturnCodeResolverTest extends TestCase
{
    private ReturnCodeResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new ReturnCodeResolver();
    }

    public function testResolvePrimaryKnownCode(): void
    {
        $this->assertSame('OK', $this->resolver->resolvePrimary(0));
    }

    public function testResolvePrimaryFieldTooLong(): void
    {
        $this->assertSame('Field too long', $this->resolver->resolvePrimary(1));
    }

    public function testResolvePrimaryTechnicalProblem(): void
    {
        $this->assertSame('Technical problem', $this->resolver->resolvePrimary(1000));
    }

    public function testResolvePrimaryUnknownCode(): void
    {
        $this->assertSame('Unknown code 9999', $this->resolver->resolvePrimary(9999));
    }

    public function testResolveSecondaryKnownCode(): void
    {
        $this->assertSame('ORDERNUMBER', $this->resolver->resolveSecondary(1));
    }

    public function testResolveSecondaryDigest(): void
    {
        $this->assertSame('DIGEST', $this->resolver->resolveSecondary(34));
    }

    public function testResolveSecondaryUnknownCode(): void
    {
        $this->assertSame('Unknown code 9999', $this->resolver->resolveSecondary(9999));
    }

    public function testResolveSecondaryZeroReturnsEmpty(): void
    {
        $this->assertSame('', $this->resolver->resolveSecondary(0));
    }

    public function testXpathIsReused(): void
    {
        // Call twice to exercise the cached xpath branch
        $this->resolver->resolvePrimary(0);
        $this->assertSame('OK', $this->resolver->resolvePrimary(0));
    }
}