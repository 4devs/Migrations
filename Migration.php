<?php

namespace FDevs\Migrations;

use FDevs\Migrations\Configuration\ConfigurationInterface;
use FDevs\Migrations\Exception\DirectionException;
use FDevs\Migrations\Exception\UnknownVersionException;

class Migration
{
    const DIRECTION_UP = 'up';
    const DIRECTION_DOWN = 'down';

    /** @var ConfigurationInterface */
    private $configuration;

    /**
     * init.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * migrate
     *
     * @param int    $to        if zero migrate to latest version
     * @param string $direction
     *
     * @return array|Response[]
     *
     * @throws UnknownVersionException
     * @throws DirectionException
     */
    public function migrate($to = 0, $direction = self::DIRECTION_UP)
    {
        $latest = $this->configuration->getLatestVersion();
        $from = $this->configuration->getCurrentVersion();
        $response = [];
        if ($to !== 0 || $latest !== $from) {
            $to = intval($to) ?: $latest;

            $migrations = $this->configuration->getMigrations();

            if (!isset($migrations[$to]) && $to > 0) {
                throw UnknownVersionException::create($to);
            }
            if (($from === $to && $direction === self::DIRECTION_UP) || ($from > $to && $direction === self::DIRECTION_UP)) {
                $message = sprintf('this direction not support with version "%s" please use "%s" direction.', $to, self::DIRECTION_DOWN);
                throw new DirectionException($message);
            }

            if ($direction !== self::DIRECTION_DOWN && $direction !== self::DIRECTION_UP) {
                throw DirectionException::create($direction);
            }

            $migrationsToExecute = $this->configuration->getMigrationsToExecute($direction, $to);

            if (count($migrationsToExecute)) {
                foreach ($migrationsToExecute as $key => $migration) {
                    $response = array_merge($response, $migration->execute($direction));
                }
            }
        }

        return $response;
    }
}
