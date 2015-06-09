<?php

namespace FDevs\Migrations\Exception;

class UnknownVersionException extends MigrationException
{
    /**
     * create
     *
     * @param string $version
     *
     * @return static
     */
    public static function create($version)
    {
        return new static(sprintf('Could not find migration version %s', $version));
    }
}
