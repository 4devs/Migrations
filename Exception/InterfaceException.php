<?php

namespace FDevs\Migrations\Exception;

class InterfaceException extends \RuntimeException
{
    /**
     * create
     *
     * @param string $interface
     *
     * @return static
     */
    public static function create($interface)
    {
        $message = sprintf('Migration must implement the MigrationInterface interface. `%s` provided instead', $interface);

        return new static($message);
    }
}
