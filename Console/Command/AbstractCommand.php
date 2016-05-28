<?php

namespace FDevs\Migrations\Console\Command;

use Symfony\Component\Console\Command\Command;
use FDevs\Migrations\Configuration\ConfigurationInterface;

abstract class AbstractCommand extends Command
{
    /** @var ConfigurationInterface */
    private $configuration;

    /**
     * set configuration.
     *
     * @param ConfigurationInterface $configuration
     *
     * @return $this
     */
    public function setMigrationConfiguration($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * get Migration Configuration.
     *
     * @return ConfigurationInterface
     */
    protected function getMigrationConfiguration()
    {
        return $this->configuration;
    }
}
