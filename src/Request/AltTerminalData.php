<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Request;

use Pronnect\GpWebPay\DigestTrait;

/**
 * Class AltTerminalData
 *
 * Alternative terminal identification for SOAP requests.
 * All fields are optional. Max lengths: terminalId=8, terminalOwner=22, terminalCity=13.
 */
class AltTerminalData
{
    use DigestTrait;

    private ?string $terminalId = null;
    private ?string $terminalOwner = null;
    private ?string $terminalCity = null;

    public function __get(string $name): mixed
    {
        return $this->$name ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->$name) && $this->$name !== null;
    }

    public function getTerminalId(): ?string
    {
        return $this->terminalId;
    }

    public function setTerminalId(string $terminalId): self
    {
        $this->terminalId = $terminalId;

        return $this;
    }

    public function getTerminalOwner(): ?string
    {
        return $this->terminalOwner;
    }

    public function setTerminalOwner(string $terminalOwner): self
    {
        $this->terminalOwner = $terminalOwner;

        return $this;
    }

    public function getTerminalCity(): ?string
    {
        return $this->terminalCity;
    }

    public function setTerminalCity(string $terminalCity): self
    {
        $this->terminalCity = $terminalCity;

        return $this;
    }

    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->terminalId,
            $this->terminalOwner,
            $this->terminalCity,
        ]);
    }
}
