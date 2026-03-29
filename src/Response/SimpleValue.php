<?php

declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\SimpleValueInterface;

/**
 * Class SimpleValue
 */
class SimpleValue
    extends Response
    implements SimpleValueInterface, DigestInterface
{
    use DigestTrait;

    protected ?string $name = null;
    protected ?string $value = null;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->name ?? null,
            $this->value ?? null,
        ]);
    }
}
