<?php

namespace FDevs\Migrations\Configuration;

use FDevs\Migrations\Version;
use FDevs\Migrations\Exception\DirectionException;
use FDevs\Migrations\Exception\UnknownVersionException;
use FDevs\Migrations\Provider\ProviderInterface;

interface ConfigurationInterface
{
    /**
     * get current version
     *
     * @return int
     */
    public function getCurrentVersion();

    /**
     * update current version
     *
     * @param int    $version
     * @param string $direction
     *
     * @return self
     * @throws DirectionException
     * @throws UnknownVersionException
     */
    public function updateVersion($version, $direction);

    /**
     * get latest version
     *
     * @return int
     */
    public function getLatestVersion();

    /**
     * get provider
     *
     * @return ProviderInterface
     */
    public function getProvider();

    /**
     * get all version sort by up
     *
     * @return array|Version[]
     */
    public function getMigrations();

    /**
     * @param string $direction
     * @param int    $to
     *
     * @return array|Version[]
     * @throws DirectionException
     */
    public function getMigrationsToExecute($direction, $to);
}
