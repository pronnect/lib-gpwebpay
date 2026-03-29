<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Response;

/**
 * Interface SubsqTransBatchStatusResponseInterface
 *
 * @api
 */
interface SubsqTransBatchStatusResponseInterface extends ResponseInterface
{
    public function getBatchStatus(): ?string;

    public function getErrorDescription(): ?string;
}
