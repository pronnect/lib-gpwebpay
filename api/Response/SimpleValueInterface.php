<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface SimpleValueInterface
 *
 * @api
 */
interface SimpleValueInterface extends ResponseInterface
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @return string|null
     */
    public function getValue(): ?string;
}
