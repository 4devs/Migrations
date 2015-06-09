<?php

namespace FDevs\Migrations\Configuration;

use Doctrine\ODM\MongoDB\DocumentManager;
use FDevs\Migrations\Exception\DirectionException;
use FDevs\Migrations\Migration;
use FDevs\Migrations\Provider\ProviderInterface;

class MongodbConfiguration extends AbstractConfiguration
{
    /** @var ProviderInterface */
    private $provider;

    /** @var \MongoCollection */
    private $collection;

    /**
     * @param array             $dirs
     * @param ProviderInterface $provider
     * @param DocumentManager   $dm
     * @param string            $collection
     * @param string|null       $db         db name if don't use default db
     */
    public function __construct(array $dirs, ProviderInterface $provider, DocumentManager $dm, $collection, $db = null)
    {
        $this->provider = $provider;
        $db = $db ?: $dm->getConfiguration()->getDefaultDB();
        $this->collection = $dm->getConnection()->selectCollection($db, $collection);

        $this->collection->ensureIndex(['version' => 1], ['unique' => true]);
        $this->setDirs($dirs);
    }

    /**
     * {@inheritDoc}
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentVersion()
    {
        $version = 0;
        $data = $this->collection->find()->sort(['version' => -1]);
        if ($data->count()) {
            $data = $data->getSingleResult();
            $version = $data['version'];
        }

        return $version;
    }

    /**
     * {@inheritDoc}
     */
    public function updateVersion($version, $direction)
    {
        if ($direction === Migration::DIRECTION_UP) {
            $insert = ['version' => $version, 'date' => new \MongoDate()];
            $this->collection->insert($insert);
        } elseif ($direction === Migration::DIRECTION_DOWN) {
            $this->collection->remove(['version' => $version]);
        } else {
            throw DirectionException::create($direction);
        }
    }
}
