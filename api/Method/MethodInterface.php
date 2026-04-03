<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayApi\Method;

use Pronnect\GpWebPayApi\DigestSignerInterface;
use Pronnect\GpWebPayApi\Request\RequestInterface;
use Pronnect\GpWebPayApi\Response\ResponseInterface;

/**
 * Interface MethodInterface
 *
 * @api
 */
interface MethodInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param                          $client
     * @param RequestInterface $request
     * @param DigestSignerInterface|null $digestSign
     *
     * @return ResponseInterface
     */
    public function __invoke(
        $client,
        RequestInterface $request,
        ?DigestSignerInterface $digestSign = null
    ): ResponseInterface;
}
