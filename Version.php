<?php

namespace FDevs\Migrations;

use FDevs\Migrations\Configuration\ConfigurationInterface;
use FDevs\Migrations\Exception\DirectionException;
use FDevs\Migrations\Exception\InterfaceException;
use FDevs\Migrations\Provider\ProviderInterface;

class Version
{
    /** @var string */
    private $class;

    /** @var ProviderInterface */
    private $provider;

    /** @var int */
    private $version = 0;

    /** @var ConfigurationInterface */
    private $configuration;

    /**
     * init.
     *
     * @param ConfigurationInterface $configuration
     * @param string                 $class
     * @param ProviderInterface      $provider
     */
    public function __construct(ConfigurationInterface $configuration, $class, ProviderInterface $provider)
    {
        $this->configuration = $configuration;
        $this->class = $class;
        if (preg_match('/.+(\d{14})$/', $class, $data)) {
            $this->version = $data[1];
        }
        $this->provider = $provider;
    }

    /**
     * create migrate
     *
     * @return MigrationInterface
     */
    public function create()
    {
        return new $this->class();
    }

    /**
     * get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * execute migration by direction
     *
     * @param string $direction
     *
     * @return array|Response[]
     *
     * @throws DirectionException
     * @throws InterfaceException
     */
    public function execute($direction)
    {
        $migration = $this->create();
        if (!$migration instanceof MigrationInterface) {
            throw InterfaceException::create($this->class);
        }
        $this->provider->prepare($migration);
        if ($direction === Migration::DIRECTION_UP || $direction === Migration::DIRECTION_DOWN) {
            $response = $this->run($migration, $direction);
        } else {
            throw DirectionException::create($direction);
        }
        $this->configuration->updateVersion($this->version, $direction);

        return $response;
    }

    /**
     * get version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * run migration
     *
     * @param MigrationInterface $migration
     * @param string             $direction
     *
     * @return array
     */
    private function run(MigrationInterface $migration, $direction)
    {
        $response = [];
        $methods = ['pre'.ucfirst($direction), $direction, 'post'.ucfirst($direction)];

        foreach ($methods as $method) {
            $time = microtime(true);
            $data = $migration->{$method}();
            if (is_string($data) || $method === $direction) {
                $data = new Response($data);
            }
            if ($data instanceof Response) {
                $data->setClassName(get_class($migration));
                $data->setMethod($method);
                $data->setTime(microtime(true) - $time);
                $response[] = $data;
                $migration->setResponseByMethod($method, $data);
            }
        }

        return $response;
    }
}
