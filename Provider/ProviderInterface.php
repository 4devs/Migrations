<?php

namespace FDevs\Migrations\Provider;

use FDevs\Migrations\MigrationInterface;

interface ProviderInterface
{
    /**
     * prepare migration
     *
     * @param MigrationInterface $migration
     *
     * @return self
     */
    public function prepare(MigrationInterface $migration);

    /**
     * is support migration
     *
     * @param MigrationInterface $migration
     *
     * @return boolean
     */
    public function isSupport(MigrationInterface $migration);
}
