<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPay\DigestTrait;
use Pronnect\GpWebPayApi\DigestInterface;
use Pronnect\GpWebPayApi\Response\LoyaltyProgramDetailsInterface;

/**
 * Class LoyaltyProgramDetails
 */
class LoyaltyProgramDetails
    extends Response
    implements LoyaltyProgramDetailsInterface, DigestInterface
{
    use DigestTrait;

    protected ?string $programNumber = null;
    protected ?string $programId = null;
    protected ?string $programName = null;
    protected ?string $programExpiryMonth = null;
    protected ?string $programExpiryYear = null;

    /**
     * @return string|null
     */
    public function getProgramNumber(): ?string
    {
        return $this->programNumber;
    }

    /**
     * @return string|null
     */
    public function getProgramId(): ?string
    {
        return $this->programId;
    }

    /**
     * @return string|null
     */
    public function getProgramName(): ?string
    {
        return $this->programName;
    }

    /**
     * @return string|null
     */
    public function getProgramExpiryMonth(): ?string
    {
        return $this->programExpiryMonth;
    }

    /**
     * @return string|null
     */
    public function getProgramExpiryYear(): ?string
    {
        return $this->programExpiryYear;
    }

    /**
     * @return string|null
     */
    public function getDigest(): ?string
    {
        return $this->makeDigest([
            $this->programNumber ?? null,
            $this->programId ?? null,
            $this->programName ?? null,
            $this->programExpiryMonth ?? null,
            $this->programExpiryYear ?? null,
        ]);
    }
}
