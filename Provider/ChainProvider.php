<?php

namespace FDevs\Migrations\Provider;

use FDevs\Migrations\MigrationInterface;

class ChainProvider implements ProviderInterface
{
    /** @var array|ProviderInterface[] */
    private $providerList = [];

    /**
     * init.
     *
     * @param array $providerList
     */
    public function __construct(array $providerList = [])
    {
        foreach ($providerList as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function prepare(MigrationInterface $migration)
    {
        foreach ($this->providerList as $provider) {
            $provider->prepare($migration);
        }

        return $this;
    }

    /**
     * add provider
     *
     * @param ProviderInterface $provider
     *
     * @return $this
     */
    public function addProvider(ProviderInterface $provider)
    {
        $this->providerList[] = $provider;

        return $this;
    }
}
