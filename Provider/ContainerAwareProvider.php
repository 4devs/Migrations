<?php

namespace FDevs\Migrations\Provider;

use FDevs\Migrations\MigrationInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareProvider implements ProviderInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * init.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(MigrationInterface $migration)
    {
        if ($this->isSupport($migration)) {
            $migration->setContainer($this->container);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupport(MigrationInterface $migration)
    {
        return $migration instanceof ContainerAwareInterface;
    }
}
