<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay\Response;

use Pronnect\GpWebPayApi\Response\ResponseInterface;
use RuntimeException;

/**
 * Class Response
 */
abstract class Response implements ResponseInterface
{
    /**
     * @param $name
     *
     * @throws RuntimeException
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            $message = sprintf(
                'Class "%s" does not have property "%s"',
                static::class,
                $name
            );
        } else {
            $message = sprintf(
                'Use class method "get%s" to get property "%s" instead of direct access',
                ucfirst($name),
                $name
            );
        }

        throw new RuntimeException($message);
    }

    /**
     * @param $name
     * @param $value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $method = "set" . ucfirst($name);
        if (method_exists($this, $method)) {
            $this->$method($value);

            return;
        }

        if (property_exists($this, $name)) {
            $this->$name = $value;

            return;
        }
        throw new RuntimeException(
            sprintf(
                'Class "%s" does not have property "%s"',
                static::class,
                $name
            )
        );
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name): bool
    {
        return property_exists($this, $name) && !empty($this->$name);
    }
}
