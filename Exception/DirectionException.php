<?php

namespace FDevs\Migrations\Exception;

use FDevs\Migrations\Migration;

class DirectionException extends MigrationException
{
    /**
     * create.
     *
     * @param string $direction
     *
     * @return static
     */
    public static function create($direction)
    {
        $message = sprintf(
            'direction "%s" not support please use "%s" and "%s"',
            $direction,
            Migration::DIRECTION_UP,
            Migration::DIRECTION_DOWN
        );

        return new static($message);
    }
}
