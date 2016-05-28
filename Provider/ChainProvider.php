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
     * {@inheritdoc}
     */
    public function prepare(MigrationInterface $migration)
    {
        foreach ($this->providerList as $provider) {
            if ($provider->isSupport($migration)) {
                $provider->prepare($migration);
            }
        }

        return $this;
    }

    /**
     * add provider.
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

    /**
     * {@inheritdoc}
     */
    public function isSupport(MigrationInterface $migration)
    {
        $support = false;
        foreach ($this->providerList as $provider) {
            if ($provider->isSupport($migration)) {
                $support = true;
                break;
            }
        }

        return $support;
    }
}
